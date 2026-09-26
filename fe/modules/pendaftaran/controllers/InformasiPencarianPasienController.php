<?php

/**
 * @Author: Naufal
 * @Date:   2018-01-18 16:04:34
 * @Description: Merupakan Controller Informasi Pencarian Pasien yang terdapat pada module pendaftaran
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\InfPencarianPasienForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\KeluargaPasienForm;
use app\modules\pendaftaran\models\AksesForm;
use Doco\components\DocoMessages;
use yii\web\UploadedFile;
use app\modules\pendaftaran\components\traits\EditPasienTrait;
use app\components\Traits\HistoryPatientTrait;
use app\components\DocoConstants;

class InformasiPencarianPasienController extends DocoController
{
    use EditPasienTrait;
    use HistoryPatientTrait;

    protected $_title = "Pencarian Pasien";
    protected $_module = 'pendaftaran/informasi-pencarian-pasien/';
    protected $_moduleDaftarRajal = 'pendaftaran/daftar-rajal/';
    protected $_page;
    protected $_restPendaftaran;
    protected $_restMaster;
    protected $_id_ruangan;
    protected $_is_hide_alias;
    protected $allowAction = ['*'];
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_page = Yii::t('fe', 'Informasi Pencarian Pasien');

        $cache = Yii::$app->cache;
        
        if (!empty($cache->get('is_hide_alias'))) {
            $this->_is_hide_alias = $cache->get('is_hide_alias');
        } else {
            $requests = $this->_restPendaftaran->post('allow/get-konfig-system');
            $response = json_decode($requests->getBody(), true);
            $response = $response['response'];
            $cache->set('is_hide_alias', $response['is_hide_alias']);
            $this->_is_hide_alias = $response['is_hide_alias'];
        }
    }

    public function actionIndex()
    {
      $session = Yii::$app->session;
     $thirdApp = Yii::$app->params->thirdApp;
      $this->setPasienEditLinkBefore('/pendaftaran/informasi-pencarian-pasien');
      $active_workspace = Yii::$app->session->get('active_workspace');
        return Yii::$app->docoPlugin->execute($this,'informasi_pencarian_pasien_index');
    }

    public function actionGetRiwayat($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/riwayat?advanced-filter[pasien_id]='.$id);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                unset($value['pasien_id']);
                $value['toggle'] = "";
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekam_medik'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekam_medik']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekam_medik']);
        }

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }

        // unset($yiiRestfulParams['advanced-filter']['display_name']);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/get-data?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['id_pasien'] = $value['pasien_id'];
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                unset($value['pasien_id']);
                $value['primary'] = $primaryKey;
                $value['toggle'] = "";

                if (isset($this->_is_hide_alias) && $this->_is_hide_alias != false) {
                    $value['nama_pasien'] = $value['nama_pasien'];
                } else {
                    $value['nama_pasien'] = $value['nama_depan'].$value['nama_pasien'];
                }

                $value['display_name'] = $value['nama_pasien'];
                $value['nomor_pasien'] = isset($value['no_mobile_pasien']) ? $value['no_mobile_pasien'] : $value['no_telepon_pasien'];

                $value['tgl_rekam_medik'] = DocoHelpers::display_label($value['tgl_rekam_medik'], true,
                    date('d M Y', strtotime($value['tgl_rekam_medik'])));
                $value['rowNum'] = $no;
                if (array_key_exists('pembuat_nama', $value)){
                    $value['petugas'] = (isset($value['petugas_nama']) ? $value['petugas_nama'] : $value['pembuat_nama']) . '<br>' . (isset($value['tgl_update_terakhir']) ? explode(" ", $value['tgl_update_terakhir'])[0] . '<br>' . explode(" ", $value['tgl_update_terakhir'])[1] : explode(" ", $value['tgl_pembuatan'])[0] . '<br>' . explode(" ", $value['tgl_pembuatan'])[1]);
                } else {
                    $value['petugas'] = '';
                }
                $value['tanggal_lahir'] = DocoHelpers::display_label($value['tanggal_lahir'], true,
                    date('d M Y', strtotime($value['tanggal_lahir'])));
                if (!empty($value['additional_pasien'])) {
                    $pasienAdds = json_decode($value['additional_pasien'], true);
                    $noid = null;
                    if (!empty($pasienAdds) && isset($pasienAdds[0]) && is_array($pasienAdds[0])) {
                        foreach($pasienAdds as $adds) {
                            if ($adds['jenisidentitas'] == DocoConstants::JENIS_KTP) {
                                $noid = $adds['no_identitas_pasien'];
                            }
                        }
                    }
                    $value['no_identitas'] = $noid;
                } else {
                    $value['no_identitas'] = null;
                }
                $value['is_catatanpenting'] = !empty(trim($value['catatanpenting_pasien'])) ? true : false;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['totalData'] = number_format($this->actionGetTotalPasien());
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionRiwayat($id)
    {
        $title = 'Detail Riwayat Pasien';
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restPendaftaran->get('inf-pencarian-pasien/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);

        if($body['metadata']['status'] == 200) {
            $infoPasien = $body['response'];
        }

        return $this->render('riwayat-pasien', get_defined_vars());
    }

    public function actionGetDataRiwayatPasien($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['pasien_id'] = $id;

        $data = [];
    
        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/riwayat', [
                'query' => $filter,
            ]);

            $body = json_decode($response->getBody(), true);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tgl_pendaftaran'] = !isset($value['tgl_pendaftaran']) ? "-" : date('d-M-Y', strtotime($value['tgl_pendaftaran']));
                $value['tglpasienpulang'] = !isset($value['tglpasienpulang']) ? "-" : date('d-M-Y', strtotime($value['tglpasienpulang']));
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
        
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionUpdate($id, $is_close = null)
    {
        $status = $this->_status; $options = $this->_options;
        $linkBeforeUpdate = $this->getPasienEditLinkBefore();
        $linkBeforeUpdateWithoutSlash = substr($linkBeforeUpdate, 1);
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new PasienForm;
        $modelPj = new PjpasienForm;
        $modelKp = new KeluargaPasienForm;
        $model->scenario = "edit-pasien";
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $dataPost = $tmpDataPj = $tmp = $tmpLastPasien = $tmpDataKp = [];
            $post = $request->post();
            $model->load($post);
            $modelPj->load($post);
            $modelKp->load($post);
            $pjId = $post['pj_id'];
            $keluargapasien_id = $post['keluargapasien_id'];
            $isDelete = $post['is_deleted_pj'];
            $isDeleteKp = $post['is_deleted_kp'];
            if(!empty($pjId) || $pjId == '0' || $isDelete == '1') {
                if($pjId || $pjId == '0') {
                    if(!$modelPj->validate()) {
                        $formName = substr(strrchr(get_class($modelPj), "\\"), 1);
                        $errors = DocoHelpers::parseError($modelPj->errors, $formName);
                        return DocoHelpers::responseTemplate(422, 'Error', $errors); 
                    } 
                }
                $tmpLastPasien = [
                    'pj_id' => $pjId,
                    'pendaftaran_id_last' => $post['pendaftaran_id'],
                    'is_deleted_pj' => $isDelete,
                ];
                $tmpDataPj = $modelPj->attributes;
                $tmp = array_merge($tmpDataPj, $tmpLastPasien);
            }

            if(!empty($keluargapasien_id) || $keluargapasien_id == '0' || $isDeleteKp == '1') {
                if($keluargapasien_id || $keluargapasien_id == '0') {
                    if(!$modelKp->validate()) {
                        $formName = substr(strrchr(get_class($modelKp), "\\"), 1);
                        $errors = DocoHelpers::parseError($modelKp->errors, $formName);
                        return DocoHelpers::responseTemplate(422, 'Error', $errors); 
                    } 
                }
                $tmp['kp_id'] = $keluargapasien_id;
                $tmp['is_deleted_kp'] = $isDeleteKp;
                $tmpDataKp = $modelKp->attributes;
                $tmp = array_merge($tmpDataKp, $tmp);
            }
            
            $model->tanggal_lahir = $model->tmp_tanggal_lahir;
            if (strtotime($model->tmp_tanggal_lahir) > strtotime(date("Y-m-d"))) {
                $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                $result['response']['text'] = Yii::t('fe', 'Tanggal Lahir Tidak Boleh Lebih dari Tanggal Hari ini');

                return DocoHelpers::response($result, 422);
            }

            // Improvement multiple jenis identitas
            if (!empty($model->no_identitas_pasien)) {
                $data = array();
                $jenisIdentitas = $model->jenisidentitas ? $model->jenisidentitas : [];
                $noIdentitas = $model->no_identitas_pasien;

                foreach ($noIdentitas as $key => $value) {
                    if (!array_key_exists($key, $jenisIdentitas)) {
                        $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                        $result['response']['text'] = Yii::t('fe', 'Terjadi kesalahan pada server, silahkan cek inputan.');
                        return DocoHelpers::response($result, 422);
                    }

                    if ($value) {
                        $data[] = [
                            'jenisidentitas' => $jenisIdentitas[$key],
                            'no_identitas_pasien' => $value
                        ];
                    } else {
                        /*$result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                        $result['response']['text'] = Yii::t('fe', 'Terjadi kesalahan pada server, silahkan cek inputan.');
                        return DocoHelpers::response($result, 422);*/
                    }
                }

                $model->jenisidentitas = '';
                $model->no_identitas_pasien = '';
                $model->nopeserta_bpjs = '';
                $model->additional_identitas = !empty($data) ? json_encode($data) : null;
            }

            $image = UploadedFile::getInstance($model, "photopasien");
            if ($model->validate()) {
                $model->pasien_id = $id;
                $dataPost = array_merge($tmp, $model->attributes);
                
                if (isset($image->name)) {
                    $ext = end(explode(".", $image->name));
                    $model->photopasien = Yii::$app->security->generateRandomString().".{$ext}";
                    $path = \Yii::getAlias('@webroot');
                    if ($image->saveAs($path.'/media/img/pasien/' . $model->photopasien)) {
                        try {
                            $response = $this->_restPendaftaran->post('pasien/update?id='.$id, [
                                'form_params' => $dataPost
                            ]);
                            $body = json_decode($response->getBody(), true);
                            if (ArrayHelper::getValue($response->getBody(), 'metadata.status') == 200) {
                                self::clearCachePendaftaran(ArrayHelper::getValue($response->getBody(), 'response.list_pendaftaran'));
                            }
                            return DocoHelpers::response($body);
                        } catch (RequestException $e) {
                            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                        } catch (\Exception $e) {
                            return DocoHelpers::responseTemplate(500, $e->getMessage());
                        }
                    } else {
                        try {
                            $response = $this->_restPendaftaran->post('pasien/update?id='.$id, [
                                'form_params' => $dataPost
                            ]);
                            $body = json_decode($response->getBody(), true);
                            if (ArrayHelper::getValue($body, 'metadata.status') == 200) {
                                self::clearCachePendaftaran(ArrayHelper::getValue($body, 'response.list_pendaftaran'));
                            }
                            return DocoHelpers::response($body);
                        } catch (RequestException $e) {
                            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                        } catch (\Exception $e) {
                            return DocoHelpers::responseTemplate(500, $e->getMessage());
                        }
                    }
                } else {
                    try {
                        $response = $this->_restPendaftaran->post('pasien/update?id='.$id, [
                            'form_params' => $dataPost
                        ]);
                        $body = json_decode($response->getBody(), true);
                        if (ArrayHelper::getValue($body, 'metadata.status') == 200) {
                            self::clearCachePendaftaran(ArrayHelper::getValue($body, 'response.list_pendaftaran'));
                        }
                        return DocoHelpers::response($body);
                    } catch (RequestException $e) {
                        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $ddlJenisKelamin = [];
            $ddlStatusPerkawinan = [];
            $ddlWargaNegara = [];
            $ddlAgama = [];
            $ddlPropinsi = [];
            $ddlkabupaten = [];
            $ddlkecamatan = [];
            $ddlkelurahan = [];
            $ddlPekerjaan = [];
            $pro_id = null;
            $kab_id = null;
            $kec_id = null;
            $kel_id = null;
            $data_lookup = [];
            $attrPjTera = [];
            try {
              $response = $this->_restPendaftaran->get('pasien/view?id='.$id);
              $body = json_decode($response->getBody(), TRUE);
              
              $attributes = $body['response']['pasien'];
              $attrPj = $body['response']['pj'];
              $attrKunjungan = $body['response']['kunjungan'];
              $attrKp = $body['response']['keluarga_pasien'];
              if (!empty($attributes['penanggungjawabtera_nama'])) {
                $attrPjTera = [
                    'penanggungjawabtera_nama' => $attributes['penanggungjawabtera_nama'],
                    'penanggungjawabtera_hubungan' => $attributes['penanggungjawabtera_hubungan'],
                    'penanggungjawabtera_alamat' => $attributes['penanggungjawabtera_alamat'],
                  ];
              }
              
              $model->attributes = $attributes;
              $model->additional_identitas = json_decode($attributes['additional_pasien']);
              $single_identitas[] = (object)[
                'jenisidentitas' => $model->jenisidentitas,
                'no_identitas_pasien' => $model->no_identitas_pasien
              ];
              if ($model->additional_identitas == null) {
                  $model->additional_identitas = $single_identitas;
              }
              if(!empty($attrPj)) {
                $modelPj->pj_pengantar = $attrPj['pengantar'];
                $modelPj->pj_nama = $attrPj['penanggungjawab_nama'];
                $modelPj->pj_jk = (int) $attrPj['penanggungjawab_jeniskelamin'];
                $modelPj->pj_jenis_identitas = $attrPj['jenisidentitas'];
                $modelPj->pj_no_identitas = $attrPj['no_identitas'];
                $modelPj->pj_hubungan = $attrPj['hubungankeluarga'];
                $modelPj->pj_tempat_lahir = $attrPj['penanggungjawab_tempatlahir'];
                $modelPj->pj_no_telepon = $attrPj['penanggungjawab_notelp'];
                $modelPj->pj_tanggal_lahir = $attrPj['penanggungjawab_tgllahir'];
                $modelPj->pj_alamat = trim(preg_replace('/\s+/', ' ', $attrPj['penanggungjawab_alamat']));
              }
              if(!empty($attrKp)) {
                $modelKp->keluarga_nama = $attrKp['keluarga_nama'];
                $modelKp->keluarga_jk = (int) $attrKp['keluarga_jk'];
                $modelKp->keluarga_hubungan = $attrKp['keluarga_hubungan'];
                $modelKp->keluarga_no_telepon = $attrKp['keluarga_no_telepon'];
                $modelKp->keluarga_alamat = $attrKp['keluarga_alamat'];
                $modelKp->keluarga_namadepan = $attrKp['keluarga_namadepan'];
                $modelKp->keluarga_pekerjaan_id = (int) $attrKp['keluarga_pekerjaan_id'];
                $modelKp->keluarga_propinsi_id = (int) $attrKp['keluarga_propinsi_id'];
                $modelKp->keluarga_kabupaten_id = (int) $attrKp['keluarga_kabupaten_id'];
                $modelKp->keluarga_kecamatan_id = (int) $attrKp['keluarga_kecamatan_id'];
                $modelKp->keluarga_kelurahan_id = (int) $attrKp['keluarga_kelurahan_id'];
                $modelKp->keluarga_rt = $attrKp['keluarga_rt'];
                $modelKp->keluarga_rw = $attrKp['keluarga_rw'];
              }
              $pro_id = $model['propinsi_id'];
              $kab_id = $model['kabupaten_id'];
              $kec_id = $model['kecamatan_id'];
              $kel_id = $model['kelurahan_id'];
              $packData = $this->_restPendaftaran->get('allow/pack-informasi-pencarian-pasien?', [
                  'query'=>[
                      'param'=>'update',
                      'propinsi_id'=>$pro_id,
                      'kabupaten_id'=>$kab_id,
                      'kecamatan_id'=>$kec_id,
                  ]
              ]);
              $packData = json_decode($packData->getBody(), true);
              $data_lookup = $packData['response']['lookup'];
              $ddlJenisKelamin = (count($packData['response']['lookup']['jenis_kelamin']) > 0) ? ArrayHelper::map($packData['response']['lookup']['jenis_kelamin'],'lookup_id','lookup_name') : [];
              $ddlGolonganDarah = (count($packData['response']['lookup']['golongan_darah']) > 0) ? ArrayHelper::map($packData['response']['lookup']['golongan_darah'],'lookup_id','lookup_name') : [];
              $ddlNamaDepan = (count($packData['response']['lookup']['nama_depan']) > 0) ? ArrayHelper::map($packData['response']['lookup']['nama_depan'],'lookup_id','lookup_name') : [];
              $ddlJenisIdentitas = (count($packData['response']['lookup']['jenis_identitas']) > 0) ? ArrayHelper::map($packData['response']['lookup']['jenis_identitas'],'lookup_id','lookup_name') : [];
              $ddlStatusPerkawinan = (count($packData['response']['lookup']['status_perkawinan']) > 0) ? ArrayHelper::map($packData['response']['lookup']['status_perkawinan'],'lookup_id','lookup_name') : [];
              $ddlWargaNegara = (count($packData['response']['lookup']['warga_negara']) > 0) ? ArrayHelper::map($packData['response']['lookup']['warga_negara'],'lookup_id','lookup_name') : [];
              $ddlAgama = (count($packData['response']['lookup']['agama']) > 0) ? ArrayHelper::map($packData['response']['lookup']['agama'],'lookup_id','lookup_name') : [];
              $ddlPropinsi = (count($packData['response']['data_propinsi']) > 0) ? ArrayHelper::map($packData['response']['data_propinsi'],'propinsi_id','propinsi_nama') : [];
              $ddlkabupaten = (count($packData['response']['data_kabupaten']) > 0) ? ArrayHelper::map($packData['response']['data_kabupaten'],'kabupaten_id','kabupaten_nama') : [];
              $ddlkecamatan = (count($packData['response']['data_kecamatan']) > 0) ? ArrayHelper::map($packData['response']['data_kecamatan'],'kecamatan_id','kecamatan_nama') : [];
              $ddlkelurahan = (count($packData['response']['data_kelurahan']) > 0) ? ArrayHelper::map($packData['response']['data_kelurahan'],'kelurahan_id','kelurahan_nama') : [];
              $ddlPekerjaan = (count($packData['response']['data_pekerjaan']) > 0) ? ArrayHelper::map($packData['response']['data_pekerjaan'],'pekerjaan_id','pekerjaan_nama') : [];
              $ddlPendidikan = (count($packData['response']['data_pendidikan']) > 0) ? ArrayHelper::map($packData['response']['data_pendidikan'],'pendidikan_id','pendidikan_nama') : [];
              $ddlSuku = (count($packData['response']['data_suku']) > 0) ? ArrayHelper::map($packData['response']['data_suku'],'suku_id','suku_nama') : [];
              $model->username = Yii::$app->docoVars->user('nama');
              $is_hide_alias = $this->_is_hide_alias;

            }catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }


            $title =  Yii::t('fe', 'Ubah Data Pasien');
            $encryptId = DocoHelpers::encrypt($id);
            return $this->render('form-update', get_defined_vars());
        }
    }

    private function clearCachePendaftaran ($data)
    {
        if (!empty($data)) {
            foreach ($data as $item) {
                if ($item['konsulpoli_id'] != null) {
                    Yii::$app->cache->delete('pasien-pendaftaran-id-' . DocoHelpers::encrypt($item['konsulpoli_id']));
                }
                Yii::$app->cache->delete('pasien-pendaftaran-id-' . DocoHelpers::encrypt($item['pendaftaran_id']));
                Yii::$app->cache->delete('data-pasien-igd-' . $item['pendaftaran_id']);
            }
        }
        return true;
    }

    // Export excel
    public function actionExportExcel()
    {
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // Try catch
        try {
            $path = Yii::getAlias("@download") . "/informasi-pencarian-pasien.xlsx";
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        }
    }

    public function actionExportPdf() {
        try {
            $path = Yii::getAlias("@download") . "/inf-pencarian-pasien.pdf";
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

            if (isset($yiiRestfulParams['advanced-filter']['tgl_rekam_medik'])) {
                $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekam_medik']);
                $tgl_awal = $tgl_pendaftaran_range[0];
                $tgl_akhir = $tgl_pendaftaran_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_rekam_medik']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
                $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
                $tgl_awal = $tgl_pendaftaran_range[0];
                $tgl_akhir = $tgl_pendaftaran_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['propinsi_nama']) && $yiiRestfulParams['advanced-filter']['propinsi_nama'] == 'Loading ...') {
                unset($yiiRestfulParams['advanced-filter']['propinsi_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['kecamatan_nama']) && $yiiRestfulParams['advanced-filter']['kecamatan_nama'] == 'Loading ...') {
                unset($yiiRestfulParams['advanced-filter']['kecamatan_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['kabupaten_nama']) && $yiiRestfulParams['advanced-filter']['kabupaten_nama'] == 'Loading ...') {
                unset($yiiRestfulParams['advanced-filter']['kabupaten_nama']);
            }

            $url = 'inf-pencarian-pasien/export-pdf?'.http_build_query($yiiRestfulParams);
            $response = $this->_restPendaftaran->get($url, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakKartu($id = null){
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/inf-pencarian-pasien.pdf";
        try {
            // if(!empty($id)){
            //     $id = DocoHelpers::decrypt($id);
            //     $url = 'inf-pencarian-pasien/cetak-kartu?id='.$id;
            //     $path = Yii::getAlias("@download") . "/cetak-kartu-{$id}.pdf";
            // }else{
            //     $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            //     $url = 'inf-pencarian-pasien/export-pdf?'.http_build_query($yiiRestfulParams);
            // }
            $url = 'inf-daftar-sepuluh-terakhir/print-kartu-pasien';
            $decryptPasienId = Docohelpers::decrypt($id);
            $response = $this->_restPendaftaran->get($url, [
                    'query' => ['pasien_id'=>$decryptPasienId],
                    'save_to' => $path,
                ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakDataPasien($id = null)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/inf-pencarian-pasien-data.pdf";
        $pasienId = $request->get('pasien_id');
        try {

            if (!empty($id)) {
                $id = DocoHelpers::decrypt($id);
            } else if (!empty($pasienId)) {
                $id = DocoHelpers::decrypt($pasienId);
            }

            $url = 'inf-pencarian-pasien/cetak-data-pasien?id='.$id;
            $path = Yii::getAlias("@download") . "/cetak-data-{$id}.pdf";

            $response = $this->_restPendaftaran->get($url, [
                    'save_to' => $path,
                ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionListKabupaten() {
        $request = Yii::$app->request;
        $post = $request->post();
        if(isset($post['depdrop_parents'][0])) {
            $propinsi = $post['depdrop_parents'][0];
            $PropinsiRequest = $this->_restPendaftaran->get('allow/list-kabupaten-new?propinsi='.$propinsi);
            $body = json_decode($PropinsiRequest->getBody(),TRUE);
            $ddlKabupaten = $body['response'];
            Yii::error([
                    'data' => $ddlKabupaten
                ]);
            $out = [];
            foreach($ddlKabupaten as $kab => $value) {
                $out[] = [
                    'id' => $value['kabupaten_id'],
                    'name' => $value['kabupaten_nama']
                ];
            }

            echo json_encode(['output'=>$out]);
            return;
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionListKecamatan() {
        $request = Yii::$app->request;
        $post = $request->post();
        if(!empty($post['depdrop_parents'][0])) {
            $kabupaten = $post['depdrop_parents'][0];

            $KecamatanRequest = $this->_restPendaftaran->get('allow/list-kecamatan-new?kabupaten='.$kabupaten);
            $body = json_decode($KecamatanRequest->getBody(),TRUE);
            $ddlKecamatan = $body['response'];
            $out = [];
            foreach($ddlKecamatan as $key => $value) {
                $out[] = [
                    'id' => $value['kecamatan_id'],
                    'name' => $value['kecamatan_nama']
                ];
            }

            echo json_encode(['output'=>$out, 'selected'=>'']);
            return;
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionRiwayatTera($id)
    {
        $title = 'Detail Riwayat Pasien Tera';
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $response = $this->_restPendaftaran->get('inf-pencarian-pasien/riwayat-tera?advanced-filter[pasien_id]='.$id,['form_params'=>[]]);
        $body = json_decode($response->getBody(), true);
        $data = [];
        $infoPasien = [];
        $data_riwayat = [];
        $data_pasien = [];
        $ttl = [];
        $no = 1;
        $isTera = true;
        
        if($body['metadata']['status'] == 200) {
            $infoDataPasien = $this->_restPendaftaran->get('inf-pencarian-pasien/view?id='.$id);
            $resInfoPasien = json_decode($infoDataPasien->getBody(), TRUE);

            if($resInfoPasien['metadata']['status'] == 200 && !empty($resInfoPasien['response'])){
                $infoPasien = $resInfoPasien['response'];
            }
            if(!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                        $data_pasien['nama_pasien'] = isset($value['nama_pasien']) ? $value['nama_pasien'] : '-';
                        $data_pasien['tanggal_lahir'] = isset($value['tanggal_lahir']) ?  date('d M Y H:i', strtotime($value['tanggal_lahir'])): '-';
                        $data_pasien['tempat_lahir'] = isset($value['tempat_lahir']) ? $value['tempat_lahir'] : '-';
                        $data_pasien['no_rekam_medik'] = isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '-';
                        $data_pasien['nama_ibu'] = isset($value['nama_ibu']) ? $value['nama_ibu'] : '-';
        
                        $data['rowNum'] = $no;
                        $data['ruangan_nama'] = isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
                        if(empty($data['ruangan_nama'])) {
                            $data['ruangan_nama'] = isset($value['instalasi_nama']) ? $value['instalasi_nama'] : '';
                        }
                        $data['tgl_pendaftaran'] = isset($value['tgl_pendaftaran']) ? date('d M Y H:i', strtotime($value['tgl_pendaftaran'])) : '-';
                        $data['no_pendaftaran'] = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '-';
                        $data['kelaspelayanan_nama'] = isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '-';
                        $data['carakeluar'] = isset($value['carakeluar']) ? $value['carakeluar'] : '-';
                        $data['status_periksa'] = isset($value['status_periksa']) ? $value['status_periksa'] : '-';
        
                        $data_riwayat[$no] = $data;
                    $no++;
                }
            }
        }
        return $this->render('riwayat-pasien', get_defined_vars());
    }

    public function actionGetRiwayatTera($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/riwayat?advanced-filter[pasien_id]='.$id);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                unset($value['pasien_id']);
                $value['toggle'] = "";
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetTotalPasien()
    {
        return $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'inf-pencarian-pasien/total-pasien',
            'payload' => []
        ]);
    }

    public function actionViewRiwayatTera()
    {
        $pasien_terra = Yii::$app->request->get('pasien_id');
        $title = Yii::t('fe','Riwayat Pasien Tera');

        return $this->render('@app/extensions/pendaftaran/views/view_riwayat_pasien_tera', get_defined_vars());
    }

    public function actionBukaAkses($pasien_id){
        
        $pasien_id = DocoHelpers::decrypt($pasien_id);
        $model = new AksesForm;
        $request = Yii::$app->request;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        $response = $this->_restRm->get('inf-pencarian-pasien/get-data-pasien?pasien_id='.$pasien_id);
        $body = json_decode($response->getBody(), True);
        $body = $body['response'];
        if (!empty($body['akses'])){
            $model->attributes = $body['akses'];
        }
        
        $arrayConfig = $body['lookup'];

        if ($request->post()){
            $model->load($request->post());
            if ($model->validate()) {
                $model->pasien_id = $body['pasien']['pasien_id'];
                $response = $this->_restRm->post('inf-pencarian-pasien/ubah-akses', [
                        'form_params' => $model->attributes
                    ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);
            }else{
                $errors = $model->errors;
                return DocoHelpers::response($errors, 422, $formName);
            }
        }
        return $this->renderAjax('buka-akses',get_defined_vars());
    }

    /* excel bg-proc */
    public function actionExcelPopup()
    {
        $title = 'Download Excel List Data Pasien';
        $url = '/pendaftaran/informasi-pencarian-pasien';
        $fileType = 'excel';
        $request = Yii::$app->request;
        $restfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $randString = DocoHelpers::generateRandomString();
        $restfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $restfulParams);
        return $this->renderAjax('modal/_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "inf-pencarian-pasien/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'LIST_DATA_PENDAFTARAN_PASIEN.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPendaftaran->get('inf-pencarian-pasien/download-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }

    /**
     * 
     * * @author : bacengjs (Bambang.Hermawan@sirs.co.id)
     * * A product of PT. Citraraya Nusatama
     * * Powered by Sirs
     *
    */

    public function actionRiwayatPerubahanDataPasien()
    {
        $title = 'Riwayat Perubahan Data Pasien';
        $pasien_id = Yii::$app->request->get('pasien_id');
        return $this->renderAjax('modal/_modalRiwayatPerubahanDataPasien', get_defined_vars());
    }

    public function actionGetDataRiwayatPerubahanDataPasien($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['pasien_id'] = DocoHelpers::decrypt($id);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/get-data-riwayat-perubahan-data-pasien', [
                'query' => $filter,
            ]);

            $body = json_decode($response->getBody(), true);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tgl_ubahdata'] =  DocoHelpers::convertDate($value['tgl_ubahdata']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
        
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionModalProfilRingkasMedisRj()
    {
        $request = Yii::$app->request;
        $pasien_id = DocoHelpers::decrypt($request->get('pasien_id'));

        $title     = Yii::t('fe', 'Profil Ringkas Medis Rawat Jalan');
        $getPack = $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'inf-pencarian-pasien/pack-modal-prmrj',
            'payload' => [
                'query' => [
                    'pasien_id' => $pasien_id,
                ]
            ]
        ]);
        $instalasi = ArrayHelper::getValue($getPack, 'instalasi', []);
        $pasien = ArrayHelper::getValue($getPack, 'pasien', []);

        $pasien['primary'] = $request->get('pasien_id');
        $pasien['umur'] = $pasien['tanggal_lahir'] ? 
            str_replace(
                "umur ", "", DocoHelpers::getUmur(ArrayHelper::getValue($pasien, 'tanggal_lahir'))
            ) : null;
        $tgl_pembuatan_resume = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $pasien['tgl_pembuatan_resume'] = $tgl_pembuatan_resume['tanggal'].' '.
            $tgl_pembuatan_resume['bulan'].' '.
            $tgl_pembuatan_resume['tahun'];
        $pasien['data_kunjungan'] = join(" & ", ArrayHelper::getColumn(
                $instalasi, 'instalasi_nama'
            ));

        return $this->renderAjax('modal/_modal_prmrj', compact('title', 'instalasi', 'pasien'));
    }

    public function actionGetDataProfilRingkasMedisRj()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $request = Yii::$app->request;

        if ($request->get('tgl_pendaftaran', null)) {
            $tgl_pendaftaran_range = explode(' - ', $request->get('tgl_pendaftaran'));
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $payload['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $payload['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        }
        if ($request->get('instalasi_id', null)) {
            $payload['advanced-filter']['instalasi_id'] = $request->get('instalasi_id');
            if ($request->get('ruangan_id', null)) {
                $payload['advanced-filter']['ruangan_id'] = $request->get('ruangan_id');
            }
        }
        $payload['advanced-filter']['pasien_id'] = DocoHelpers::decrypt($request->get('pasien_id'));

        $response = $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'inf-pencarian-pasien/get-data-prmrj',
            'method' => 'get',
            'payload' => [
                'query' => $payload
            ]
        ]);

        $no = 1;
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['rowNum'] = $no;
            $response['data'][$key]['tgl_pendaftaran'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $diagnosa = (array) json_decode($value['a_diag_utama']);
            $response['data'][$key]['diagnosa'] = $diagnosa['text'];
            $no++;
        }

        return $response;
    }

    public function actionCetakPdfProfilRingkasMedisRj()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = [
            'pasien_id' => DocoHelpers::decrypt($request->get('pasien_id')),
            'tgl_pendaftaran' => $request->get('tgl_pendaftaran', null),
            'instalasi_id' => $request->get('instalasi_id', null),
            'ruangan_id' => $request->get('ruangan_id', null),
        ];

        $url = 'inf-pencarian-pasien/cetak-pdf-prmrj';
        $path = Yii::getAlias("@download") . "/profil-ringkas-medis-rawat-jalan.pdf";
        $response = $this->_restPendaftaran->get($url, [
            'query' => $params,
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    /* pdf bg-proc */
    // public function actionPdfPopup()
    // {
    //     $title = 'Download PDF List Data Pasien';
    //     $url   = '/pendaftaran/informasi-pencarian-pasien';
    //     $fileType = 'pdf';
    //     $request  = Yii::$app->request;
    //     $restfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
    //     $randString    = DocoHelpers::generateRandomString();
    //     $restfulParams['randString'] = $randString;
    //     Yii::$app->session->setFlash($randString, $restfulParams);
    //     return $this->renderAjax('_modalExcel', get_defined_vars());
    // }

    public function actionUploadDokumen()
    {
        $title = 'Upload Dokumen';
        $request = Yii::$app->request;
        $pasien_id = $request->get('id', null);
        $id = DocoHelpers::decrypt($pasien_id);
        try {
            $response = $this->_restPendaftaran->get('inf-pencarian-pasien/get-data-ruangan-dokter', [
                'query' => $id,
            ]);
            $body = json_decode($response->getBody(), true);
            $result['dokter'] = (!empty($body['response']['dokter'])) ? $body['response']['dokter'] : [];
            $result['ruangan'] = (!empty($body['response']['ruangan'])) ? $body['response']['ruangan'] : [];
            $dokter = ArrayHelper::map($result['dokter'], 'pegawai_id', 'nama_pegawai');
            $ruangan = ArrayHelper::map($result['ruangan'], 'ruangan_id', 'ruangan_nama');
    
            return $this->renderAjax('modal/_modalUpload', get_defined_vars());
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
       
    }
}
