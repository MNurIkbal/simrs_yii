<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-02-26 07:57:05
 * @Last Modified by:   afil
 * @Last Modified time: 2018-02-27 15:54:03
 *
 * @Refactored by : Anggoro
 * @Refactored date : 2019-07-01
 *
 * @Description: Pendaftaran IGD / Rawat Darurat
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\pendaftaran\components\Lookup;

use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\master\models\CaraBayarForm;
use app\modules\master\models\PenanggungjawabForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;
use GuzzleHttp\Exception\RequestException;

use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class DaftarIgdController extends DocoController
{
    protected $_title = 'Pendaftaran Rawat Darurat';
    protected $_module = '/pendaftaran/daftar-igd';
    protected $_moduleRedirect = '/pendaftaran/daftar-igd';
    protected $allowAction = ['*'];
    protected $_restPendaftaran;

    protected $_id_carabayar_bpjs;

    protected $_instalasi_id_rd = DocoConstants::INSTALASI_ID_RD;
    use PendaftaranTrait;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;

        $carabayarRequest = $this->_restPendaftaran->get('allow/get-carabayar-bpjs');
        $body = json_decode($carabayarRequest->getBody(),TRUE);
        $carabayar = $body['response'];

        $this->_id_carabayar_bpjs = $carabayar['carabayar_id'];
    }

    /**
     *
     */
    public function actionIndex($id_booking = null)
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $params = 'igd';
        $ruanganId = $active_workspace['ruangan_id'];
        $ruanganNama = $active_workspace['ruangan_name'];
        $module = $this->_module;
        $title = $this->_title;
        $modelPasien = new PasienForm;
        $modelPasien->scenario = "pendaftaran-igd";
        $modelKunjungan = new KunjunganForm();
        $tipePasien = new TipePasienForm;
        $modelRujukan = new RujukanForm;
        $modelPj = new PjpasienForm;
        $modelPj->scenario = 'pendaftaran-igd';
        $modelAsuransi = new AsuransiForm;
        $modelAsuransi->scenario = DocoConstants::WS_IGD;
        $modelBpjs = new BpjsNewForm;
        $multiPayer = new MultiCarabayarForm;
        $multiPayer->scenario = 'asuransi-igd';
        $modelKunjungan->scenario = 'with_mandatory_pjawab';
        $modelKunjungan->konfig_referral_required = $modelKunjungan->konfig_referral_required = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                                                    && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';
        try {
            if (Yii::$app->request->post()) {
                return $this->actionSimpanKunjungan($params);
            }
            $instalasi_id = $this->_instalasi_id_rd;
            $instalasi_id = json_encode($instalasi_id);
            $ruanganId = $active_workspace['ruangan_id'];
            $instalasi_workspace = $active_workspace['instalasi_id'];
            $konfig_pendaftaran = $this->_restPendaftaran->get('allow/get-konfig-system');
            $konfig_pendaftaran = json_decode($konfig_pendaftaran->getBody(), true);
            $pemilihanDokter = is_null($konfig_pendaftaran["response"]["is_pemilihandokter"]) ? false : $konfig_pendaftaran["response"]["is_pemilihandokter"];

            $dataForm = $this->getDataApi($instalasi_id, 2);
            $modelPasien->propinsi_id = $dataForm['defaultPropinsi'];
            $modelPasien->kabupaten_id = $dataForm['defaultKota'];
            $rujukan_dari = $dataForm['rujukan_dari'];
            $render_pasien_data = [
                "tipePasien" => $tipePasien,
                "modelKunjungan" => $modelKunjungan,
                "modelRujukan" => $modelRujukan,
                'modelPasien' => $modelPasien,
                'modelBpjs' => $modelBpjs,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'optionsProv' => $dataForm['optionsProv'],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => null,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'default_asal_rujukan' => $dataForm['default_asal_rujukan'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_hide_alias' => isset($dataForm['konfigSystem']['is_hide_alias']) ? $dataForm['konfigSystem']['is_hide_alias'] : null,
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer
            ];

            $render_kunjungan_data = [
                "modelKunjungan" => $modelKunjungan,
                "modelPj" => $modelPj,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'kelaspelayanan' => $dataForm["kelas_pelayanan"],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => null,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'modelAsuransi' => $modelAsuransi,
                'modelBpjs' => $modelBpjs,
                'ruanganId' => $ruanganId,
                'default_jenis_penyakit' => $dataForm['default_jenis_penyakit'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_limit_tagihan' => $konfig_pendaftaran['response']['is_limit_tagihan'],
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer,
                'optionsRuangan' => $dataForm['optionsRuangan'],
                'show_referal' => $dataForm['konfigSystem']['show_referal_pendaftaran'],
            ];

            $penjaminIntegrasi = $dataForm['penjaminIntegrasi'];
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (Exception $e) {
            $render_pasien_data =
            $render_kunjungan_data = [];
        }
    	return $this->render('index', get_defined_vars());
    }

    public function actionUbahPasien($id, $param)
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $model = new PasienForm;
        if ($request->post()) {
            $model->load($post['PasienForm']);
            try {
                $image = UploadedFile::getInstance($model, "photopasien");
                if(isset($image->name)){
                    $path = \Yii::getAlias('@webroot').'/media/img/pasien/';
                    $oldFile = $path . $post['PasienForm']['photopasien'];
                    if ($post['PasienForm']['photopasien'] && file_exists($oldFile)) {
                        unlink($oldFile); // hapus file lama
                    }
                    $ext = end(explode(".", $image->name));
                    $rand = substr(Yii::$app->security->generateRandomString(), 0, 7);
                    $post['PasienForm']['photopasien'] = $rand . ".{$ext}";
                    if(!$image->saveAs($path . $post['PasienForm']['photopasien'])){
                        return DocoHelpers::responseTemplate(500,'Terjadi Kesalahan simpan gambar');
                    }
                }

                $requests = $this->_restPendaftaran->post('pasien/update?id='.$id,
                    ['form_params' => $post]
                );
                $response = json_decode($requests->getBody(), true);
                return DocoHelpers::response($response);
            } catch (RequestException $e) {
                echo DocoHelpers::dataTabelsException($e->getMessage());
            } catch (\Exception $e) {
                echo DocoHelpers::dataTabelsException($e->getMessage());
            }
        }
        return $this->renderAjax('partial/_pasien', get_defined_vars());
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganPasien()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $pasien_id = $request->get('pasien_id', null);
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if ($pasien_id) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-igd/get-data-kunjungan-pasien?pasien_id='.$pasien_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $data = $body['response']['data'];
                // dump($data);die;
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $no++;
                        $value['tgl_pendaftaran'] = $value['tgl_pendaftaran'] != '' ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false) : '';
                        unset($value['pasien_id']);
                        $data[$key] = $value;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                    $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                }
                else {
                    $result['data'] = $data;
                    $result['recordsTotal'] = 0;
                    $result['recordsFiltered'] = 0;
                }
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionTambahPasien()
    {
        try {
            $post = Yii::$app->request->post();
            $model = new PasienForm();
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->load($post);
            if ($model->validate()) {
                $image = UploadedFile::getInstance($model, "photopasien");
                if (isset($image->name)) {
                    $ext = end(explode(".", $image->name));
                    $rand = substr(Yii::$app->security->generateRandomString(), 0, 7);
                    $model->photopasien = $model->no_rekam_medik . '-' . $rand . ".{$ext}";
                    $path = \Yii::getAlias('@webroot') . '/media/img/pasien/';
                    if (!$image->saveAs($path . $model->photopasien)) {
                        return DocoHelpers::responseTemplate(500, 'Terjadi Kesalahan simpan gambar');
                    }
                }
                $requests = $this->_restPendaftaran->post(
                    'pasien/create',
                    ['form_params' => $model->attributes]
                );
                $response = json_decode($requests->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'PasienForm');

            }


        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (Exception $e) {
            return DocoHelpers::responseJsonString($e->getMessage(),$formName);
        }
    }

    public function actionGetPasienAutofill($nopesertabpjs)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response_field = [
            'nik' => '',
            'namapasien' => '',
            'tanggallahir' => '',
            'jeniskelamin' => '',
            'alamatpasien' => '',
            'provinsi' => '',
            'kabupaten' => '',
            'kecamatan' => '',
            'kelurahan' => ''
        ];
        try{
            $getDataRequest = $this->_restPendaftaran->get('allow/get-pasien-autofill',[
                'query' => [
                    'nopesertabpjs' => $nopesertabpjs
                ]
            ]);
            $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
            $response = $parsingRequest['response'];
            return $response;
        }catch (RequestException $e) {
            return $response_field;
        } catch (\Exception $e) {
            return $response_field;
        }
    }

    /*public function actionGetDataSepuluhTerakhir()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $param = $request->get('param');
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $response = $this->_restPendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/index', [
                'form_params'=>[],
                'query'=>['user_id'=>$loginpemakai_id, 'jenis'=>$param]
            ]);

            $body = json_decode($response->getBody(),TRUE);
            $response = $body['response']['data'];
            $no = $request->get('start',1);

            foreach ($response as $key => $value) {
                $no++;
                $primaryPendaftaran = DocoHelpers::encrypt($value['pendaftaran_id']);
                $primaryPasien = DocoHelpers::encrypt($value['pasien_id']);
                $value['ruangan_nama'] = $param == 'ranap'
                    ? $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur']
                    : $value['ruangan_nama'];
                $value['rowNum'] = $no;
                $value['primaryPendaftaran'] = $primaryPendaftaran;
                $value['primaryPasien'] = $primaryPasien;
                $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($response);
            $result['recordsFiltered'] = count($response);
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }*/

    /**
    * @author rizfardi@docotel.com
    * @param string id pendaftaran_id encrypted
    */
    /*public function actionUpdate($id)
    {
        // Init
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        $model = new PendaftaranForm;
        $model->scenario = 'update';
        $modelBpjs = new BpjsForm;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);

        // $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $post = $request->post();
            $model->attributes = $post['PendaftaranForm'];
            $modelBpjs->attributes = $post['BpjsForm'];

            if ($model->validate()) {
                if ($model->carabayar_id == $this->_id_carabayar_bpjs) {
                    $modelBpjs->no_rekam_medik = $model->no_rekam_medik;
                    if ($modelBpjs->validate()) {
                        $response = $this->_restPendaftaran->post('bpjs/create', [
                            'form_params' => $modelBpjs->attributes
                        ]);
                        $responseBpjs = json_decode($response->getBody(),true);
                        $model->bpjs_id = $responseBpjs['response']['bpjs_id'];
                    } else {
                        $errors = DocoHelpers::parseError($modelBpjs->errors, 'BpjsForm');
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                }

                $response = $this->_restPendaftaran->put('tra-pasien-igd/update?id='.$id, [
                    'form_params' => $model->attributes
                ]);

                $session = Yii::$app->session;
                $session->set('trans_update_success', Yii::t('fe', 'Data rawat darurat berhasil diubah.'));
                return DocoHelpers::responseTemplate(200, 'OK');
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'PendaftaranForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } else {
            $response = $this->_restPendaftaran->get('tra-pasien-igd/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;

            $dokterRequest = $this->_restMaster->get('pegawai/allow-list-dokter-rajal');
            $body = json_decode($dokterRequest->getBody(),TRUE);
            $dokterList = $body['response'];

            $penyakitRequest = $this->_restMaster->get('jenis-kasus-penyakit/allow-list-penyakit');
            $body = json_decode($penyakitRequest->getBody(),TRUE);
            $penyakitList = $body['response'];

            $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar?default=0');
            $body = json_decode($carabayarRequest->getBody(),TRUE);
            $carabayarList = $body['response'];

            $penjaminRequest = $this->_restMaster->get('penjamin/list-penjamin?carabayar_id='.$model->carabayar_id);
            $body = json_decode($penjaminRequest->getBody(),TRUE);
            $penjaminList = $body['response'];

            $data_referensi = $this->_restPendaftaran->get('bpjs/allow-get-data-referensi');
            $body = json_decode($data_referensi->getBody(), TRUE);
            $list_data_referensi = $body['response'];

            return $this->render('form_update', get_defined_vars());
        }
    }*/
}
