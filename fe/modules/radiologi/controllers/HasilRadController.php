<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Radiologi
 * @copyright 26 Juli 2018 aweutist
 */

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\radiologi\components\traits\UploadDokumenTrait;
use Doco\laboratorium\models\SpecimentForm;
use yii\helpers\ArrayHelper;
use app\modules\radiologi\models\PasienMasukPenunjangForm;

class HasilRadController extends DocoController
{
    use UploadDokumenTrait;
    
    protected $_title = "Hasil Pemeriksaan Radiologi";
    protected $_module = '/radiologi/hasil-rad';
    protected $_restRad;
    protected $_id_ruangan;
    protected $backendUrl;
    protected $serviceRest;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
    }

    public function actionIndex($id, $pelayananId = null, $tindakanId = null)
    {
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $cache = Yii::$app->cache;
        $title = $this->_title;
        $data = $this->getDataApi($id, $pelayananId, $tindakanId);
        $dataCatatan = ArrayHelper::getValue($data, 'data_catatan');
        $data_pasien = ArrayHelper::getValue($data, 'data_pasien', []);
        $diagnosa = ArrayHelper::getValue($data_pasien, 'nama_diagnosa');
        $diagnosa = !empty($diagnosa) ? json_decode($diagnosa, true) : null;
        $diagnosa = ArrayHelper::getValue($diagnosa, 'text');
        $dokter_pengirim = ArrayHelper::getValue($data_pasien, 'dokter_perujuk_nama');
        $statusPeriksa = ArrayHelper::getValue($data_pasien, 'status_periksa');
        $hasilRad = ArrayHelper::getValue($data, 'data_hasil_rad', []);
        $isReferred = ArrayHelper::getValue($hasilRad, 'is_referred', false);
        $is_bayar = ArrayHelper::getValue($hasilRad, 'status_bayar', false);
        $is_bayar = ($is_bayar) ? 1 : 0;
        $pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
        $pendaftaran_id = ArrayHelper::getValue($data_pasien, 'pendaftaran_id');
        $pasienadmisi_id = ArrayHelper::getValue($data_pasien, 'pasienadmisi_id');
        $pegawai_id = ArrayHelper::getValue($data_pasien, 'pegawai_id');
        $tgl_verifikasi = !empty($hasilRad['tgl_verifikasi']) ? date('d M Y H:i:s', strtotime($hasilRad['tgl_verifikasi'])) : '';
        $additionalData = !empty($hasilRad['additional_data']) ? json_decode($hasilRad['additional_data'], true) : [];
        $dataBatalVerif = !empty($additionalData['batal_verif']) ? $additionalData['batal_verif'] : [];
        $tglBatalVerifikasi = !empty($dataBatalVerif['tgl_batal']) ? date('d M Y H:i:s', strtotime($dataBatalVerif['tgl_batal'])) : '';
        $catatan = ArrayHelper::getValue($data_pasien, 'catatan_dokterpengirim');
        $catatanPenunjang = ArrayHelper::getValue($dataCatatan, 'catatan');
        $catatan = empty($catatanPenunjang) ? $catatan : $catatanPenunjang;
        $dokter = ArrayHelper::getValue($data_pasien, 'dokter_penunjang');
        $count_expertise = $data['count_data_expertise'];
        $is_periksa = ($statusPeriksa == DocoConstants::LAB_ST_PEN_PERIKSA) ? true : false;
        
        return $this->render('index', get_defined_vars());
    }

    private function getDataApi($id = null, $pelayananId = null, $tindakanId = null)
    {
        $id = DocoHelpers::decrypt($id);
        $pelayananId = ($pelayananId != "null") ? DocoHelpers::decrypt($pelayananId) : null;
        $tindakanId = ($tindakanId != "null") ? DocoHelpers::decrypt($tindakanId) : null;
        $response = $this->_restRad->request('GET', 'hasil-rad/generate-api',[
            'query' => [
                'id' => $id,
                'pelayananId' => $pelayananId,
                'tindakanId' => $tindakanId,
            ]
        ]);
        $body = json_decode($response->getBody(),TRUE);
        $return = [
            'data_pasien' => $body['response']['data-pasien'],
            'count_data_expertise' => $body['response']['count-data-expertise'],
            'data_hasil_rad' => $body['response']['data-hasil-rad'],
            'data_catatan' => $body['response']['data-catatan'],
        ];

        return $return;
    }

    public function actionGetData($id, $pelayananId = null, $tindakanId = null)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $pelayananId = DocoHelpers::decrypt($pelayananId);
            $tindakanId = DocoHelpers::decrypt($tindakanId);
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['id'] = $id;
            $yiiRestfulParams['pelayananId'] = $pelayananId;
            $yiiRestfulParams['tindakanId'] = $tindakanId;
            $response = $this->_restRad->request('get', 'hasil-rad/index', [
                    'query' => $yiiRestfulParams
            ]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                $tindakanpelayanan_id = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                $penunjang_id = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $status = DocoHelpers::encrypt($value['status_periksa']);
                $value['primary'] = $primaryKey;
                $value['tindakan_id'] = $tindakanpelayanan_id;
                $value['hasilpemeriksaanrad_id'] = DocoHelpers::encrypt($value['hasilpemeriksaanrad_id']);
                $value['penunjang_id'] = $penunjang_id;
                $value['status'] = $status;
                unset($value['daftartindakan_id']);
                $value['rowNum'] = $no;
                $value['cyto_tindakan'] = (!$value['cyto_tindakan']) ? '' : '&#10003;';
                $value['tgl_ambilfoto'] = (!empty($value['tgl_ambilfoto']))
                    ? date('d M Y H:i:s', strtotime($value['tgl_ambilfoto'])) 
                    : '';
                $value['tgl_uploadhasil'] = (!empty($value['tgl_uploadhasil']))
                    ? date('d M Y H:i:s', strtotime($value['tgl_uploadhasil'])) 
                    : '';
                $value['tgl_hasilrad'] = (!empty($value['tgl_hasilrad']))
                    ? date('d M Y H:i:s', strtotime($value['tgl_hasilrad'])) 
                    : '';
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => count($body['response']),
                'recordsFiltered' => count($body['response'])
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCetakHasil($id, $tindakan_id, $penunjang_id, $hasilpemeriksaanrad_id = null)
    {
        $daftartindakan_id = DocoHelpers::decrypt($id);
        $tindakanpelayanan_id = DocoHelpers::decrypt($tindakan_id);
        $penunjang_id = DocoHelpers::decrypt($penunjang_id);
        $request = Yii::$app->request;
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $jenis_cetakan = $request->get('jenis_cetakan', null);
        if(!empty($jenis_cetakan)) {
            $jenis_cetakan = DocoHelpers::decrypt($jenis_cetakan);
        }
        $pendaftaran_id = $request->get('pendaftaran_id');
        $modulAlias = $activeWorkspace['modul_alias'];
        $urlReport = 'hasil-radiologi';
        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport, [
                   'queryParameter' => [
                        'modul' => $modulAlias,
                        'tindakan_id' => $daftartindakan_id,
                        'id' => $tindakanpelayanan_id,
                        'penunjang_id' => $penunjang_id,
                        'hasilpemeriksaanrad_id' => !empty($hasilpemeriksaanrad_id) ? DocoHelpers::decrypt($hasilpemeriksaanrad_id) : null,
                        'halaman' => "2",
                        'jenis_cetakan' => $jenis_cetakan
                   ],
            ]);
        }
    }

    public function actionCetak($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $jenis_cetakan = $request->get('jenis_cetakan', null);
        if(!empty($jenis_cetakan)) {
            $jenis_cetakan = DocoHelpers::decrypt($jenis_cetakan);
        }
        $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $modulAlias = $activeWorkspace['modul_alias'];
        try {
            $response = $this->_restRad->get('input-hasil/cetak-hasil-pdf', [
                'save_to' => $path,
                'query' => [
                    'modul' => $modulAlias,
                    'pasienmasukpenunjang_id' => $id,
                    'jenis_cetakan' => $jenis_cetakan,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path,$response);
        } catch (RequestException $e) {                                                                              
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakLabel($id, $tindakan_id, $penunjang_id)
    {
        //HasilRadLabel
        $id = DocoHelpers::decrypt($id); //1075 - daftartindakan_id
        $tindakan_id = DocoHelpers::decrypt($tindakan_id); //24804 - tindakanpelayanan_id
        $penunjang_id = DocoHelpers::decrypt($penunjang_id); //354 - pasienmasukpenunjang_id
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-label.pdf";
        try {
            $response = $this->_restRad->get('hasil-rad/cetak-label?id=' . $id . '&tindakan_id=' . $tindakan_id . '&penunjang_id=' . $penunjang_id, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionVerifikasi($id, $hasilId)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $hasilId = DocoHelpers::decrypt($hasilId);
        $tindakanId = $request->get('tindakan_id', null);
        if(!empty($tindakanId)) {
            $tindakanId = DocoHelpers::decrypt($tindakanId);
        }
        try {
            $response = $this->_restRad->request('PUT', 'hasil-rad/verifikasi', [
                'query' => [
                    'id' => $id,
                    'hasilId' => $hasilId,
                    'tindakanId' => $tindakanId,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionBatalVerifikasi($id, $hasilId)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $hasilId = DocoHelpers::decrypt($hasilId);
        $tindakanId = $request->get('tindakan_id', null);
        if(!empty($tindakanId)) {
            $tindakanId = DocoHelpers::decrypt($tindakanId);
        }
        try {
            $response = $this->_restRad->request('PUT', 'hasil-rad/batal-verifikasi', [
                'query' => [
                    'id' => $id,
                    'hasilId' => $hasilId,
                    'tindakanId' => $tindakanId,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionConfirmPeriksa($id, $tindakanpelayanan_id = null, $daftartindakan_id = null)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        // if($tindakanpelayanan_id) {
        //     $tindakanpelayanan_id = DocoHelpers::decrypt($tindakanpelayanan_id);
        // }
        // if($daftartindakan_id) {
        //     $daftartindakan_id = DocoHelpers::decrypt($daftartindakan_id);
        // }
        $title = Yii::t('fe', 'Mulai pemeriksaan');
        $sub_title = $this->_title;
        $list_data = $this->getListDataApi();
        $data_pegawai = $list_data["data_pegawai"];
        $modelMasukPenunjang = new PasienMasukPenunjangForm;
        $form_name = substr(strrchr(get_class($modelMasukPenunjang), "\\"), 1);
        $is_disabled = 'true';
        $response = $this->_restRad->get('allow/get-pasien?id='.$id);
        $response = json_decode($response->getBody(), true);
        $data_pasien = $response["response"]["data"];
        $additionalParams = '';
        if(!empty($tindakanpelayanan_id)) {
            $additionalParams .= '&pelayananId='.$tindakanpelayanan_id;
        }
        if(!empty($daftartindakan_id)) {
            $additionalParams .= '&tindakanId='.$daftartindakan_id;
        }
        if ($modelMasukPenunjang->load($data_pasien, '')) {
            $modelMasukPenunjang->pegawai_id = $data_pasien['pegawai_id'];
            if ($request->post()){
                $data = $request->post('PasienMasukPenunjangForm');
                $modelMasukPenunjang->pegawai_id = $data["pegawai_id"];
                $modelMasukPenunjang->pasienmasukpenunjang_id = $id;
                $modelMasukPenunjang->tglmasukpenunjang = $data['tglmasukpenunjang'];
                if($modelMasukPenunjang->validate()) {
                    $response = $this->_restRad->post('hasil-rad/ubah-dokter', [
                        'form_params' => $modelMasukPenunjang->attributes
                    ]);
                    $id = DocoHelpers::encrypt($id);
                    $response = json_decode($response->getBody(), true);
                    $additionalParams = '';
                    if(!empty($tindakanpelayanan_id)) {
                        $additionalParams .= '&pelayananId='.$tindakanpelayanan_id;
                    }
                    if(!empty($daftartindakan_id)) {
                        $additionalParams .= '&tindakanId='.$daftartindakan_id;
                    }
                    $redirect = '/radiologi/hasil-rad/index?id='.$id.$additionalParams;
                    return json_encode(['url'=>$redirect]);
                    $this->redirect(['hasil-rad/index?id='.$id]);
                }
                else {
                    return DocoHelpers::response($modelMasukPenunjang->errors, 422, $form_name);
                }
                
            }else{
                return $this->renderAjax('_confirm_periksa', get_defined_vars());
            }
        }
        else {
            return DocoHelpers::responseTemplate(
                500,
                Yii::t('fe', 'Kegagalan Pada Sistem'),
                [],
                ['title' => Yii::t('fe', 'Gagal Mengubah Dokter'), 'text' => Yii::t('fe', 'Data tidak ditemukan.')]
            );
        }
    }

    private function getListDataApi()
    {
        $request = Yii::$app->request;
        $response = $this->_restRad->get('allow/get-list-data?id_ruangan='.$this->_id_ruangan);
        $body = json_decode($response->getBody(),TRUE);
        $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
        $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
        $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
        $result = [
            'data_statusperiksa' => $data_statusperiksa,
            'data_pegawai' => $data_pegawai,
            'data_penjamin' => $data_penjamin,
        ];

        return $result;
    }

    public function actionUpdateCatatan($id)
    {
        $request = Yii::$app->request;
        return $this->guzzleExec($this->_restRad, [
            'method' => 'POST',
            'returnResponse' => true,
            'url' => 'hasil-rad/update-catatan',
        ], [
            'query' => [
                'id' => $id
            ],
            'form_params' => [
                'catatan' => $request->post('catatan')
            ]
        ]);
    }

}
