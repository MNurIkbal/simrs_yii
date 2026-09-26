<?php 

/*
* @Author: Sunarko / Master Tempat Tidur
* @Date:   2018-07-23 17:16:31
* @Last Modified by:  
* @Last Modified time: 
*/

// Namespace
namespace Doco\master\controllers;

// Using Yii
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

// Using Guzzles
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

// Using model
use app\modules\master\models\TempatTidurForm;
use app\modules\master\models\KamarTempatTidurForm;


class TempatTidurController extends DocoController
{

    protected $_title = "Tempat Tidur";
    protected $_module = '/master/TempatTidurForm';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $data = $this->getData();
       
        $data_ruangan = !empty($data['data_ruangan'])? ArrayHelper::map($data['data_ruangan'], 'ruangan_nama', 'ruangan_nama'): [];
        $data_kamar = !empty($data['data_kamar'])? ArrayHelper::map($data['data_kamar'], 'kamarruangan_nokamar', 'kamarruangan_nokamar'): [];
        $status = ['true'=>'Aktif', 'false'=>'Tidak Aktif'];
        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $title = Yii::t('fe', 'Tambah Tempat Tidur');
        $model = new TempatTidurForm;
        $modelKamar = new KamarTempatTidurForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        $data_kamar = [];
        if ($post) {
            if ($model->validate()) {
                $model->no_tempattidur = trim($model->no_tempattidur);
                $model->is_active = true;
                $model->is_terisi = !empty($model->is_terisi) ? true : false;
                $response = $this->_restMaster->request('POST', 'tempat-tidur/save-data',[
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                $res_status = $response['metadata']['status'];

                if ($res_status == 200) {
                    $data_dashboard['reload'] = 1;
                    $mode = Yii::$app->params->mode;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'display-dashboard-kamar-'.$mode,
                        'message' => json_encode(['data' => $data_dashboard])
                    ]);
                }
                return DocoHelpers::response($response,false,'TempatTidurForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'TempatTidurForm');
                return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
            }
        } 
        $data = $this->getData();
        $data_ruangan = !empty($data['data_ruangan']) 
        ? ArrayHelper::map($data['data_ruangan'], 'ruangan_id', 'ruangan_nama')
        : [];

        return $this->render('form', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'tempat-tidur/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kamartempattidur_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey,'change-status');
                $value['integrasi_aplikasi'] = DocoHelpers::switchStatus($value['is_rekapkinerjaprofesi'], $primaryKey,'change-status-integrasi');
                $disableOccupied = ArrayHelper::getValue($value, 'is_rekapkinerjaprofesi', false) == true ? false : true;
                $value['is_terisi'] = DocoHelpers::switchStatusV2($value['is_terisi'], $primaryKey, 'change-status-occupied', $disableOccupied);
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionChangeStatus($id, $status)
    {
        try {
            $decryptedId = DocoHelpers::decrypt($id);
            $response = $this->_restMaster->request('post', 'tempat-tidur/change-status', [
                'form_params' => [
                    'status' => $status
                ],
                'query' => [
                    'id' => $decryptedId
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $res_status = $body['metadata']['status'];

            if ($res_status == 200) {
                $data_dashboard['reload'] = 1;
                $mode = Yii::$app->params->mode;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'display-dashboard-kamar-'.$mode,
                    'message' => json_encode(['data' => $data_dashboard])
                ]);
            }

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result = [
                'response' => [
                    'title' => 'Proses Gagal!',
                    'message' => 'Terjadi Kesalahan pada sistem!'
                ]
            ];
            return DocoHelpers::response($result, 500);
        }
    }
    private function getData($id='')
    {
        try {
            if ($id) {
                $response = $this->_restMaster->request('GET', 'tempat-tidur/generate-api?id='.$id);
            }else{
                $response = $this->_restMaster->request('GET', 'tempat-tidur/generate-api');
            }
            
            $body = json_decode($response->getBody(),TRUE);
            
            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                // 'data_status' => $body['response']['data-status'],
                'data_kamar' => $body['response']['data-kamar'],
            ];
            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->request('DELETE', 'tempat-tidur/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            // if($response['response']['metadata']['status'] != 200){
            //     return DocoHelpers::response($response, 422);
            // }
            $res_status = $response['metadata']['status'];

            if ($res_status == 200) {
                $data_dashboard['reload'] = 1;
                $mode = Yii::$app->params->mode;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'display-dashboard-kamar-'.$mode,
                    'message' => json_encode(['data' => $data_dashboard])
                ]);
            }
            return DocoHelpers::response($response['response']);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],422);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],422);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/tempattidur.pdf";
        try {
            $response = $this->_restMaster->get('tempat-tidur/cetak-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionUpdate($id)
    {
        $title = Yii::t('fe', 'Ubah');
        $id = DocoHelpers::decrypt($id);
        $model = new TempatTidurForm;
        $modelKamar = new KamarTempatTidurForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        
        if ($post) {
            $dataact = $post['KamarTempatTidurForm']['is_active'];
            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'tempat-tidur/update?id=' . $id.'&act='.$dataact, [
                    'form_params' => $model->attributes,
                ]);
                $response = json_decode($response->getBody(), true);
                // var_dump($response); die;
                $res_status = $response['metadata']['status'];

                if ($res_status == 200) {
                    $data_dashboard['reload'] = 1;
                    $mode = Yii::$app->params->mode;
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'display-dashboard-kamar-'.$mode,
                        'message' => json_encode(['data' => $data_dashboard])
                    ]);
                }
                return DocoHelpers::response($response,false,'TempatTidurForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'TempatTidurForm');
                return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
            }
        } else {
            $response = $this->_restMaster->get('tempat-tidur/view-data?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $modelKamar->attributes = $body['response'];
            $modelKamar->is_active = ($modelKamar->is_active) ? '1' : '0' ;
            $model->attributes = $body['response'];
            $model->status_isi = ($model->status_isi) ? '1' : '0' ;
            $model->kettempattidur_nama = $modelKamar->kettempattidur_id;
            $model->ruangan_id = $body['response']['ruangan_id'];

            $model->kamarruangan_nokamar = $modelKamar->kettempattidur_id;
            $data = $this->getData($model->ruangan_id);
            $data_ruangan = !empty($data['data_ruangan']) 
                ? ArrayHelper::map($data['data_ruangan'], 'ruangan_id', 'ruangan_nama')
                : [];
            $data_status = ['1'=>'Isi', '0'=>'Kosong'];
            $data_kamar = !empty($data['data_kamar']) 
            ? ArrayHelper::map($data['data_kamar'], 'kamarruangan_id', 'kamarruangan_nokamar')
            : [];
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        try {
            $path = Yii::getAlias("@download") . "/Master - Tempat Tidur.xlsx";
            $response = $this->_restMaster->get('tempat-tidur/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetNamaRuangan()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'tempat-tidur/data-nama-ruangan',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['ruangan_nama'], 'text' => $value['ruangan_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNamaKamar()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'tempat-tidur/data-nama-kamar',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['kamarruangan_nokamar'], 'text' => $value['kamarruangan_nokamar']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataKamar($ruangan_id='')
    {   
        try{
            $data = [];
            if(isset($ruangan_id)){
                $response = $this->_restMaster->get('tempat-tidur/get-data-kamar?ruangan_id='.$ruangan_id);
                $body = json_decode($response->getBody(), True);
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['kamarruangan_id'], 'text' => $value['kamarruangan_nokamar']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionChangeStatusIntegrasiBpjs()
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        $status = $request->get('status');
        $response = $this->_restMaster->request('post', 'tempat-tidur/change-status-integrasi-bpjs', [
            'form_params' => ['status' => $status],
            'query' => ['id' => $id]
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }

    public function actionChangeStatusOccupied()
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        $status = $request->get('status');
        $response = $this->_restMaster->request('post', 'tempat-tidur/change-status-occupied', [
            'form_params' => ['status' => $status],
            'query' => ['id' => $id]
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }

}
