<?php

namespace app\modules\ranap\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\ranap\models\KelahiranBayiForm;
use app\modules\ranap\models\PersalinanForm;
use app\modules\ranap\models\KalaSatuForm;
use app\modules\ranap\models\KalaDuaForm;
use app\modules\ranap\models\KalaTigaForm;

/** Kala Empat */
use app\modules\ranap\models\KalaEmpatForm;
use app\modules\ranap\models\KalaEmpatDetailForm;


trait PemeriksaanPartografTrait
{
    public function actionPartograf()
    {
        try {
            $pendaftaranId = $this->_pendaftaran_id;

            return $this->renderAjax('partograf/_index',get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Fungsi untuk menampilkan konten keadaan umum
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionKeadaanUmum()
    {
        try {
            $pendaftaranId = $this->_pendaftaran_id;
            $pasienAdmisiId = $this->_pasienadmisi_id;
            $decryptedPendaftaranId = DocoHelpers::decrypt($pendaftaranId);
            $model = new PersalinanForm;
            $tglPendaftaran = $this->_data_pasien['tgl_pendaftaran'];

            if (Yii::$app->request->post()) {
                $persalinanForm = Yii::$app->request->post('PersalinanForm');

                if (!$model->load($persalinanForm, '')) {
                    $errors = $model->errors;
                    return DocoHelpers::response($errors, 422, substr(strrchr(get_class($model), "\\"), 1));
                }

                $model->tgl_persalinan = date('Y-m-d H:i:00', strtotime($model->tgl_persalinan));
                if ($model->validate()) {
                    $restRanap = $this->_restRanap->post('keadaan-umum/simpan-persalinan', [
                        'form_params' => $model
                    ]);
                    $body = json_decode($restRanap->getBody(), true);
                    $data = $body['response'];

                    return DocoHelpers::response($data);
                } else {
                    $errors = $model->errors;
                    return DocoHelpers::response($errors, 422, substr(strrchr(get_class($model), "\\"), 1));
                }
            } else {
                $restRanap = $this->_restRanap->get('keadaan-umum/get-data-keadaan-umum?id='.$decryptedPendaftaranId);
                $body = json_decode($restRanap->getBody(), true);
                $data = $body['response'];
                $persalinan = $data['persalinan'];
                $rujukKala = $data['rujukKala'];
                $pendamping = $data['pendamping'];
                $masalahPersalinan = $data['masalahPersalinan'];
                $jenisPersalinan = $data['jenisPersalinan'];

                $model->load($persalinan, '');
                $model->pendaftaran_id = $decryptedPendaftaranId;
                $model->pasienadmisi_id = $pasienAdmisiId;

                if ($model->tgl_persalinan == '') {
                    $model->tgl_persalinan = date('d-m-Y H:i');
                } else {
                    $model->tgl_persalinan = DocoHelpers::convDateTime(date('d-m-Y H:i:00', strtotime($model->tgl_persalinan)), false, true);
                }

                return $this->renderAjax('partograf/_keadaanumum', get_defined_vars());
            }
        } catch (RequestException $e) {
            return $e->getMessage();
            return $this->renderPartial('partograf/halaman_error', get_defined_vars());
        } catch (\Exception $e) {
            return $e->getMessage();
            return $this->renderPartial('partograf/halaman_error', get_defined_vars());
        }
    }

    /**
     * @todo Fungsi untuk mencetak keadaan umum
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakKeadaanUmum($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-keadaan-umum.pdf";

            $restRanap = $this->_restRanap->get('keadaan-umum/cetak-keadaan-umum', [
                'save_to' => $path,
                'query' => [
                    'id' => $id,
                ],
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionContentPartograf()
    {
        $params = Yii::$app->request;
        $tabname = $params->get('tabname',null);
        try{
            switch ($tabname) {
                case 'partograf_keadaanumum':
                    return $this->renderAjax('partograf/_keadaanumum', []);
                    break;
                case 'partograf_kalasatu':
                    return $this->renderAjax('partograf/_kalasatu', []);
                    break;
                case 'partograf_kaladua':
                    return $this->renderAjax('partograf/_kaladua', []);
                    break;
                case 'partograf_kalaempat':
                    return $this->renderAjax('partograf/_kalaempat', []);
                    break;
                case 'partograf_bayibarulahir':
                    return $this->renderAjax('partograf/_bayibarulahir', []);
                    break;
                default:
                    return $this->renderAjax('partograf/_default', []);
                    break;
            }
        }catch(\Exception $e){
            return 'Gagal Mengambil Data';
        }
    }

    public function actionPartografBayibarulahir()
    {
        try {
            $params = Yii::$app->request;
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id','MQ'));
            $kelahiranbayi_id = $params->get('kelahiranbayi_id') ? DocoHelpers::decrypt($params->get('kelahiranbayi_id')) : null;
            $mBayiBaruLahir = new KelahiranBayiForm;

            if ($params->post()) {
                $postKelahiran = $params->post('KelahiranBayiForm',[]);
                $formName = substr(strrchr(get_class($mBayiBaruLahir), "\\"), 1);
                $mBayiBaruLahir->attributes = $postKelahiran;
                if(!$mBayiBaruLahir->validate()){
                    $response = $mBayiBaruLahir->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
                if (date("Y-m-d H:i:s", strtotime($postKelahiran['tgl_lahir'])) > date("Y-m-d H:i:s")) {
                    return $this->responseJson(200, 'Tanggal lahir tidak boleh lebih dari saat ini.', [
                        'messageError' => 'Tanggal lahir tidak boleh lebih dari saat ini.'
                    ]);
                }
                $request = $this->_restRanap->post('bayibarulahir/create', [
                        'query' => [
                            'pendaftaran_id' => $pendaftaran_id,
                            'pasienadmisi_id' => $this->_data_pasien['pasienadmisi_id'],
                            'kelahiranbayi_id' => $kelahiranbayi_id
                        ],
                        'form_params' => $params->post()
                    ]);
                $response = json_decode($request->getBody(), true);
                return DocoHelpers::response($response, false);
            }
            $listData = [];
            try{
                $bundleReq = $this->_restRanap->get('bayibarulahir/bundle-data',[
                    'query' => ['id'=>$pendaftaran_id]
                ]);
                $resultReq = json_decode($bundleReq->getBody(),true);

                $listData = @$resultReq['response'];
            } catch(\Exception $e){
                $listData = [];
            } catch(\RequestException $e){
                $listData = [];
            }


            return $this->renderAjax('partograf/_bayibarulahir',get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDataBayibarulahir()
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw = $params->get('draw', 1);
            $data = [];
            $id = $params->get('id','MQ');
            $pendaftaran_id = DocoHelpers::decrypt($id);

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }
            if (isset($yiiRestfulParams['q'])) {
                unset($yiiRestfulParams['q']);
            }
            if (isset($yiiRestfulParams['filters'])) {
                unset($yiiRestfulParams['filters']);
            }
            if (isset($yiiRestfulParams['per-page'])) {
                unset($yiiRestfulParams['per-page']);
            }

            $request = $this->_restRanap->get('bayibarulahir/index?pendaftaran_id='.$pendaftaran_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);
            // Inisiasi nomor
            $no = $params->get('start', 1);
            foreach ($response['response']["data"] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kelahiranbayi_id']);
                unset($value['kelahiranbayi_id']);

                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = 0;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /*
    public function actionSimpanBayibarulahir()
    {
        try{
            $request = Yii::$app->request;
            $model = new KelahiranBayiForm;

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
    */

    public function actionKalaSatu($id)
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $this->_pendaftaran_id;
            $pasienAdmisiId = $this->_pasienadmisi_id;
            $decryptedPendaftaranId = DocoHelpers::decrypt($pendaftaranId);
            $model = new KalaSatuForm;
            $data = [];
            if($request->post()){
                $model->load($request->post());
                $model->pendaftaran_id = $decryptedPendaftaranId;
                $model->pasienadmisi_id = $pasienAdmisiId;
                if($model->validate()){
                    $response = $this->_restRanap->post('keadaan-umum/simpan-persalinan', [
                        'form_params' => $model,
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if(isset($body['metadata']['status']) != 200){
                        return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
                    }
                    return DocoHelpers::response($body['response']);
                }else{
                    $errors = $model->errors;
                    return DocoHelpers::response($errors, 422, substr(strrchr(get_class($model), "\\"), 1));
                }
            }
            try{
                $response = $this->_restRanap->get('kala/get-data-kala', [
                    'query' => [
                        'id' => $decryptedPendaftaranId,
                        'admisi' => $pasienAdmisiId,
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                $data = $body['response'];
            } catch(\Exception $e){
                $data = [];
            } catch(\RequestException $e){
                $data = [];
            }
            $model->attributes = $data;
            $model->k1_gariswaspada = isset($data['k1_gariswaspada']) ? ($data['k1_gariswaspada'] != null) ? true : 0 : false;
            return $this->renderAjax('partograf/_kalasatu', get_defined_vars());
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
        }
    }

    /**
     * @todo Fungsi untuk menampilkan konten kala dua
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionKalaDua($id)
    {
        try {
            $pendaftaranId = $this->_pendaftaran_id;
            $pasienAdmisiId = $this->_pasienadmisi_id;
            $decryptedPendaftaranId = DocoHelpers::decrypt($pendaftaranId);
            $model = new KalaDuaForm;
            $data = [];
            $k2_tindakanjanin = [];
            $k2_tindakandistosia = [];

            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();
                $model->load($post);
                $model->pendaftaran_id = $decryptedPendaftaranId;
                $model->pasienadmisi_id = $pasienAdmisiId;

                if (isset($post['k2_tindakanjanin']) && !empty($post['k2_tindakanjanin'])) {
                    $model->k2_tindakanjanin = json_encode($post['k2_tindakanjanin']);
                }

                if (isset($post['k2_tindakandistosia']) && !empty($post['k2_tindakandistosia'])) {
                    $model->k2_tindakandistosia = json_encode($post['k2_tindakandistosia']);
                }

                if ($model->validate()) {
                    $response = $this->_restRanap->post('keadaan-umum/simpan-persalinan', [
                        'form_params' => $model,
                    ]);
                    $body = json_decode($response->getBody(), true);

                    if (isset($body['metadata']['status']) != 200){
                        return DocoHelpers::response(['response' => ['title' => Yii::t('fe', 'Proses Gagal')]], 500);
                    }

                    return DocoHelpers::response($body['response']);
                } else {
                    $errors = $model->errors;
                    return DocoHelpers::response($errors, 422, substr(strrchr(get_class($model), "\\"), 1));
                }
            }

            try {
                $response = $this->_restRanap->get('kala/get-data-kala', [
                    'query' => [
                        'id' => $decryptedPendaftaranId,
                        'admisi' => $pasienAdmisiId,
                        'kala' => 2,
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                $data = $body['response'];
                $pendamping = $data['pendamping'];
            } catch (\Exception $e){
                $data = [];
                $pendamping = [];
            } catch (\RequestException $e){
                $data = [];
                $pendamping = [];
            }

            $model->attributes = $data['kala'];

            if ($model->k2_tindakanjanin != null) {
                $k2_tindakanjanin = json_decode($model->k2_tindakanjanin);

                if (isset($k2_tindakanjanin) && count($k2_tindakanjanin) > 0 && isset($k2_tindakanjanin[0]) && $k2_tindakanjanin[0] == '') {
                    $k2_tindakanjanin = [];
                }
            }

            if ($model->k2_tindakandistosia != null) {
                $k2_tindakandistosia = json_decode($model->k2_tindakandistosia);

                if (isset($k2_tindakandistosia) && count($k2_tindakandistosia) > 0 && isset($k2_tindakandistosia[0]) && $k2_tindakandistosia[0] == '') {
                    $k2_tindakandistosia = [];
                }
            }

            if ($model->k2_episitomi != 1) {
                if ($model->k2_episitomi === false) {
                    $model->k2_episitomi = 0;
                } else {
                    $model->k2_episitomi = -1;
                }
            }

            if ($model->k2_gawatjanin != 1) {
                if ($model->k2_gawatjanin === false) {
                    $model->k2_gawatjanin = 0;
                } else {
                    $model->k2_gawatjanin = -1;
                }
            }

            if ($model->k2_distosiabahu != 1) {
                if ($model->k2_distosiabahu === false) {
                    $model->k2_distosiabahu = 0;
                } else {
                    $model->k2_distosiabahu = -1;
                }
            }

            return $this->renderAjax('partograf/_kaladua', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
        }
    }

    public function actionKalaTiga($id)
    {
        try {
            $model = new KalaTigaForm;
            $k3 = [];
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();
                $model->load($post);
                $model->pendaftaran_id =  DocoHelpers::decrypt($id);

                if ($model->validate()) {
                    $response = $this->_restRanap->post('keadaan-umum/save-kala-tiga', [
                        'form_params' => $model,
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body);
                }else{
                    $errors = $model->errors;
                    return DocoHelpers::response($errors, 422, substr(strrchr(get_class($model), "\\"), 1));
                }
            }

            try {
                $response = $this->_restRanap->get('keadaan-umum/get-kala-tiga', [
                    'query' => [
                        "id" => DocoHelpers::decrypt($id)
                    ],
                ]);
                $body = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $k3 = $pendaftaran_id = [];
            }

            try {
                $response = $this->_restRanap->get("allow/get-kala-tiga-data");
                $data_body = json_decode($response->getBody(),true);
            } catch (Exception $e) {
                $data_body = [];
            }

            $k3 = isset($body["response"]["k3"]) ? $body["response"]["k3"] : null;
            $persalinan_id = isset($body["response"]["persalinan_id"]) ? $body["response"]["persalinan_id"] : null;
            $model->persalinan_id = $persalinan_id;
            $model->attributes = json_decode($k3, true);
            return $this->renderAjax('partograf/_kalatiga', get_defined_vars());
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }
    }

    public function actionKalaEmpat($id)
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $this->_pendaftaran_id;
            $pasienAdmisiId = $this->_pasienadmisi_id;
            $model = new KalaEmpatForm;
            $modelDetail = new KalaEmpatDetailForm;
            $decryptedPendaftaranId = DocoHelpers::decrypt($pendaftaranId);
            if ($request->post()) {
                $model->load($request->post());
                $model->pasienadmisi_id = $pasienAdmisiId;
                $model->pendaftaran_id = $decryptedPendaftaranId;
                if ($model->validate()) {
                    $response = $this->_restRanap->post('keadaan-umum/simpan-kala-empat', [
                        'form_params' => $model->attributes,
                        'query' => [
                            'id' => $decryptedPendaftaranId
                        ]
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body,false,'KalaEmpatForm');
                }
                return DocoHelpers::response($model->errors,422, 'KalaEmpatForm');
            }

            $response = $this->_restRanap->get('kala/get-data-kala', [
                'query' => [
                    'id' => $decryptedPendaftaranId,
                    'kala' => 4,
                    'admisi' => $pasienAdmisiId
                ]
            ]);
            $body = json_decode($response->getBody(),true);
            $model->attributes = isset($body['response']['kala']) ? $body['response']['kala'] : [];

            return $this->renderAjax('partograf/_kalaempat', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['response' => [
                'title' => 'Proses Gagal',
                'message' => $e->getMessage()
            ]], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['response' => [
                'title' => 'Proses Gagal',
                'message' => $e->getMessage()
            ]], 500);
        }
    }

    public function actionSimpanPemantauan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $modelDetail = new KalaEmpatDetailForm;
        $modelDetail->load($request->post());
        $modelDetail->pendaftaran_id = $id;
        if ($modelDetail->validate()) {
            try {
                $modelDetail->suhu  = str_replace(',','.',$modelDetail->suhu);
                $response = $this->_restRanap->post('kala/simpan-pemantauan', [
                    'query' => [
                        'id' => $id
                    ],
                    'form_params' => $modelDetail->attributes
                ]);
                $body = json_decode($response->getBody(),true);
                return DocoHelpers::response($body,false,'KalaEmpatDetailForm');
            } catch (RequestException $e) {
                return DocoHelpers::response(['response' => [
                    'title' => 'Proses Gagal',
                    'message' => $e->getMessage()
                ]], 500);
            } catch (\Exception $e) {
                return DocoHelpers::response(['response' => [
                    'title' => 'Proses Gagal',
                    'message' => $e->getMessage()
                ]], 500);
            }
        }
        return DocoHelpers::response($modelDetail->errors,422, 'KalaEmpatDetailForm');
    }

    public function actionGetDataPemantauan($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $row = $result = [];
        $result['data'] = $row;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRanap->post('kala/get-detail-persalinan', [
                'query' => [
                    'id' => $id
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $jam = [
                '' => ''
            ];
            for ($x = 1; $x <= 24; $x++) {
                $jam[$x] = $x;
            }
            $row[]  = [
                'jam_ke' => '<div class="form-group">'.
                        Html::dropDownList('KalaEmpatDetailForm[jam_ke]', null, $jam,[
                            'class' => 'select2 form-control'
                        ]) . '</div>',
                'waktu' => 
                    '<div class="col-md-12">'.
                        '<div class="input-group">'.
                            Html::input('text', 'KalaEmpatDetailForm[waktu]', null, [
                                'class' => 'form-control',
                                'id' => 'date-time-picker'
                            ]) . 
                            '<span class="input-group-addon kv-datetime-remove" title="Clear field">
                                <i class="glyphicon glyphicon-remove kv-dp-icon"></i>
                            </span>' .
                            '<span class="input-group-addon kv-datetime-picker" title="Select date &amp; time">
                                <i class="glyphicon glyphicon-calendar kv-dp-icon"></i>
                            </span>' .
                        '</div>'.
                     '</div>',
                'td_hmhg' => 
                    '<div class="col-md-12">'.
                        '<div class="input-group">'.
                            Html::input('text', 'KalaEmpatDetailForm[td_systolic]', null, [
                                'class' => 'form-control text-right doco-number',
                                'id' => 'td_systolic-detail',
                                'maxlength' => 5
                            ]) . 
                            '<span class="input-group-addon">
                                Mm
                            </span>' .
                            Html::input('text', 'KalaEmpatDetailForm[td_diastolic]', null, [
                                'class' => 'form-control text-right doco-number',
                                'id' => 'td_diastolic-detail',
                                'maxlength' => 5
                            ]) . 
                            '<span class="input-group-addon">
                                Hg
                            </span>' .
                        '</div>'.
                     '</div>',
                'detak_nadi' => 
                        '<div class="col-md-12">'.
                            Html::input('text', 'KalaEmpatDetailForm[detak_nadi]', null, [
                                'class' => 'form-control text-right doco-number',
                                'id' => 'detak_nadi-detail',
                                'maxlength' => 5
                            ]).
                        '</div>',
                'suhu' => 
                        '<div class="col-md-12">'.
                            Html::input('text', 'KalaEmpatDetailForm[suhu]', null, [
                                'class' => 'form-control text-right doco-decimal-wcomma',
                                'id' => 'suhu-detail',
                                'maxlength' => 5
                            ]).
                         '</div>',
                'tinggi_fundus' => 
                        '<div class="col-md-12">'.
                            Html::input('text', 'KalaEmpatDetailForm[tinggi_fundus]', null, [
                                'class' => 'form-control text-right doco-number',
                                'id' => 'tinggi_fundus-detail',
                                'maxlength' => 5
                            ]).
                        '</div>',
                'kontraksi_uterus' => 
                        '<div class="col-md-12">'.
                            Html::input('text', 'KalaEmpatDetailForm[kontraksi_uterus]', null, [
                                'class' => 'form-control',
                                'id' => 'kontraksi_uterus-detail',
                            ]).
                        '</div>',
                'kandung_kemih' => 
                        '<div class="col-md-12">'.
                            Html::input('text', 'KalaEmpatDetailForm[kandung_kemih]', null, [
                                'class' => 'form-control',
                                'id' => 'kandung_kemih-detail',
                            ]).
                        '</div>',
                'darah_keluar' => 
                        '<div class="col-md-12">'.
                            Html::input('text', 'KalaEmpatDetailForm[darah_keluar]', null, [
                                'class' => 'form-control text-right doco-number',
                                'id' => 'darah_keluar-detail',
                                'maxlength' => 5
                            ]).
                        '</div>',
                'aksi' => '
                    <button type="button" id="tambah-pemantauan" class="btn btn-xs btn-labeled btn-info" >
                        <b><i class="fa fa-plus"></i></b> Tambah
                    </button>
                ',
            ];

            foreach ($body['response']['data'] as $key => $value) {
                $primary = $value['persalinandetail_id'];
                $row[]  = [
                    'jam_ke' => $value['jam_ke'],
                    'waktu' => date('d-M-Y H:i:s', strtotime($value['waktu'])),
                    'td_hmhg' => $value['td_systolic'] . ' / ' . $value['td_diastolic'],
                    'detak_nadi' => $value['detak_nadi'],
                    'suhu' => str_replace('.',',',$value['suhu']),
                    'tinggi_fundus' => $value['tinggi_fundus'],
                    'kontraksi_uterus' => $value['kontraksi_uterus'],
                    'kandung_kemih' => $value['kandung_kemih'],
                    'darah_keluar' => $value['darah_keluar'],
                    'aksi' => '
                            <button type="button" id="hapus-pemantauan" data-id="'. $primary .'" class="btn btn-xs btn-labeled btn-danger" >
                                <b><i class="fa fa-trash"></i></b> Hapus
                            </button>
                        ',
                ];
            }

            $result['data'] = $row;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'] + 1;
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'] + 1;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
        }
    }

    public function actionHapusPemantauanKala($id)
    {
        try {
            $response = $this->_restRanap->request('DELETE', 'kala/hapus-pemantauan',[
                            'query' => [
                                'id' => $id
                            ]
                        ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data kondisi bayi baru lahir
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKondisiBayiBaruLahir()
    {
        try {
            $params = Yii::$app->request->get();
            $id = $params['kelahiranbayi_id'];
            $decryptedId = DocoHelpers::decrypt($id);

            $restRanap = $this->_restRanap->get('bayibarulahir/view?id='.$decryptedId);
            $body = json_decode($restRanap->getBody(), true);
            $response = $body['response'];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return $this->renderPartial('partograf/halaman_error', get_defined_vars());
        } catch (\Exception $e) {
            return $this->renderPartial('partograf/halaman_error', get_defined_vars());
        }
    }

    public function actionDeleteBayiBaruLahir()
    {
        try {
            $params = Yii::$app->request->get();
            $kelahiranbayi_id = DocoHelpers::decrypt($params['kelahiranbayi_id']);
            $restRanap = $this->_restRanap->get('bayibarulahir/delete-bayi?kelahiranbayi_id='.$kelahiranbayi_id);
            $body = json_decode($restRanap->getBody(), true);
            $response = $body['response'];
            
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['response' => ['title' => 'Proses Gagal']], 500);
        }
    }
}