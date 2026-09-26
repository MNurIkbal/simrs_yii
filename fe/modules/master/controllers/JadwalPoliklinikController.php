<?php
// Author : Naufal Ziyad L

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use app\modules\master\models\JadwalPoliklinikForm;
use GuzzleHttp\Exception\RequestException;
use app\assets\CalenderAssets;

class JadwalPoliklinikController extends DocoController
{
    protected $_title = "Master :: Jadwal Poliknik";
    protected $_module = 'master/jadwal-poliklinik/';
    protected $_restMaster;
    const SINGKATAN_RJ = 'RJ';

    public function init()
    {
        parent::init();
        CalenderAssets::register(Yii::$app->view);
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $status = [
            1 => Yii::t('fe', 'Aktif'),
            0 => Yii::t('fe', 'Tidak Aktif')
        ];
        $model = new JadwalPoliklinikForm;
        $ruangan = [];
        $hari = [];
        $shift = [];
        try{
            $ruanganRequest = $this->_restMaster->get('jadwal-poliklinik/get-options',[
                    'query' => [
                        'instalasi_id' => DocoConstants::INSTALASI_ID_RJ
                    ]
                ]);
            $body = json_decode($ruanganRequest->getBody(),true);
            $ruangan = $body['response']['ruangan'];
            $hari = $body['response']['hari'];
            $shift = ArrayHelper::map($body['response']['shift'],'shift_id','shift_nama');
            $konfigKuota = $body['response']['konfig']['kuota_antrian'];
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDataJadwalPoliklinik()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('jadwal-poliklinik/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['jadwalbukapoli_id']);
                $value['primary'] = $primaryKey;
                unset($value['jadwalbukapoli_id']);
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['rowNum'] = $no;
                $value['shift_nama'] = $value['shift']['shift_nama'];
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

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Lihat');
        $model = new JadwalPoliklinikForm;
        $id = DocoHelpers::decrypt($id);

        try{
            $response = $this->_restMaster->get('jadwal-poliklinik/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('view', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Tambah');
        $model = new JadwalPoliklinikForm;
        $status = $this->_status;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $hari = $model::getHari();
        
        if ($request->post()) {
            $model->load($request->post());
            if (!empty($model->shift_id)) {
                $model->scenario = 'shift';
            }
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('jadwal-poliklinik/create', [
                        'form_params' => $model->attributes
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                return DocoHelpers::response($model->errors,422,'JadwalPoliklinikForm');
            }
        } else {

            try {
                $ruanganRequest = $this->_restMaster->get('jadwal-poliklinik/get-options',[
                        'query' => [
                            'instalasi_id' => DocoConstants::INSTALASI_ID_RJ
                        ]
                    ]);
                $body = json_decode($ruanganRequest->getBody(),true);
                $ruangan = $body['response']['ruangan'];
                $hari = $body['response']['hari'];
                $konfig = $body['response']['konfig']['kuota_antrian'];
                $shift = ArrayHelper::map($body['response']['shift'],'shift_id','shift_nama');
                $shift_options = [];
                if(is_array($body['response']['shift']) && count($body['response']['shift'])>0){
                    foreach ($body['response']['shift'] as $v_shift) {
                        $shift_options[$v_shift['shift_id']] = [
                                                    'data-jam_awal' => $v_shift['shift_jamawal'],
                                                    'data-jam_akhir' => $v_shift['shift_jamakhir']
                                                ];
                    }
                }
                $model->is_active = 1;
            } catch (RequestException $e) {

            } catch (\Exception $e) {

            }
            
            return $this->renderAjax('_form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new JadwalPoliklinikForm;
        $status = $this->_status;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $hari = $model::getHari();
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('jadwal-poliklinik/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                return DocoHelpers::response($model->errors,422,'JadwalPoliklinikForm');
            }
        } else {         
            $ruanganRequest = $this->_restMaster->get('jadwal-poliklinik/get-options',[
                    'query' => [
                        'instalasi_id' => DocoConstants::INSTALASI_ID_RJ
                    ]
                ]);
            $body = json_decode($ruanganRequest->getBody(),true);
            $ruangan = $body['response']['ruangan'];
            $hari = $body['response']['hari'];
            $konfig = $body['response']['konfig']['kuota_antrian'];
            $shift = ArrayHelper::map($body['response']['shift'],'shift_id','shift_nama');
            $shift_options = [];
            if(is_array($body['response']['shift']) && count($body['response']['shift'])>0){
                foreach ($body['response']['shift'] as $v_shift) {
                    $shift_options[$v_shift['shift_id']] = [
                                                'data-jam_awal' => $v_shift['shift_jamawal'],
                                                'data-jam_akhir' => $v_shift['shift_jamakhir']
                                            ];
                }
            }
            $response = $this->_restMaster->get('jadwal-poliklinik/view',[
                'query' => [
                    'id' => $id ?:null
                ]
            ]);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderAjax('_update', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('jadwal-poliklinik/delete?id='.$id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('jadwal-poliklinik/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionCheckJadwalDokter()
    {
        try{
            $post = Yii::$app->request->post();
            $id = $post['id'];
            $response = $this->_restMaster->get('jadwal-poliklinik/check-jadwal-dokter',['query'=>['id'=>$id]]);
            $body = json_decode($response->getBody(),TRUE);
            $data = $body['response'];

            return DocoHelpers::response($data);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCheckJadwalPoli()
    {
        try{
            $post = Yii::$app->request->post();
            $hari = $post['hari'];
            $ruangan_id = $post['ruangan_id'];
            $response = $this->_restMaster->get('jadwal-poliklinik/check-jadwal-poli',['query'=>['hari'=>$hari,'ruangan_id'=>$ruangan_id]]);
            $body = json_decode($response->getBody(),TRUE);
            $data = $body['response'];

            return DocoHelpers::response($data);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportExcel(
        $ruangan_id=null, $shift_id=null,$hari=null,$jam_mulai=null,$jam_selesai=null)
    {

       Yii::$app->response->format = Response::FORMAT_JSON;
        $path = Yii::getAlias("@download") . "/Master Jadwal Poliklinik.xlsx";
        // var_dump($path);die();
        try {
            // $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restMaster->get("jadwal-poliklinik/export-excel?ruangan_id={$ruangan_id}&shift_id={$shift_id}&hari={$hari}&jam_mulai={$jam_mulai}&jam_selesai={$jam_selesai}", [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            return DocoHelpers::downloadFile($path, true);
           } catch (RequestException $e){
                $result['error'] = $e->getMessage();
                return $result;
           } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
           }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/jadwal-poliklinik.pdf";
        try {
            $response = $this->_restMaster->get('jadwal-poliklinik/cetak-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        // var_dump($request->post());die;
        try {
            $response = $this->_restMaster->get('jadwal-poliklinik', [
                'query' => $request->post()
            ]);

            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $ruangan = isset($response['data_ruangan']) ? $response['data_ruangan'] : [];
            $data_jadwal = isset($response['jadwal_data']) ? $response['jadwal_data'] : [];
            return DocoHelpers::response([
                'ruangan' => $ruangan,
                'data_jadwal' => $data_jadwal
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actionGetDataShift()
    {
        try{
            $post = Yii::$app->request->post();
            $shift_id = $post['shift_id'];
            $response = $this->_restMaster->get('jadwal-poliklinik/get-data-shift',['query' => ['shift_id' => $shift_id]]);
            $body = json_decode($response->getBody(),TRUE);
            $data = $body['response'];

            return DocoHelpers::response($data);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
