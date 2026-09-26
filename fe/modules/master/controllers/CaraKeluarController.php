<?php 

/*
* @Author: Sunarko / Master Cara Keluar
* @Date:   2018-07-25 10:28:24
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
use app\modules\master\models\CaraKeluarForm;

class CaraKeluarController extends DocoController
{

    protected $_title = "Cara Keluar";
    protected $_module = '/master/CaraKeluarForm';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $carakeluarinacbg = $this->getRequest();
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'cara-keluar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['carakeluar_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                if ($value['is_active']) {
                    $value['is_active'] = "Aktif";
                }else{
                    $value['is_active'] = "Tidak Aktif";
                }
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

    public function actionCreate()
    {
        try {
            $title = 'Tambah Cara Keluar';
            $model = new CaraKeluarForm;
            $request = Yii::$app->request;
            
            $carakeluarinacbg = $this->getRequest();
            
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'cara-keluar/save-data',[
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'CaraKeluarForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'CaraKeluarForm');
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
                }
            } else {
                $model->is_active = 1;
                return $this->renderPartial('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id)
    {
        $title = 'Ubah Cara Keluar';
        $model = new CaraKeluarForm;
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        $carakeluarinacbg = $this->getRequest();
        if ($post) {
            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'cara-keluar/edit-data?id=' . $id, [
                    'form_params' => $model->attributes,
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response,false,'CaraKeluarForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'CaraKeluarForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        } else {
            $response = $this->_restMaster->get('cara-keluar/view-data?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $model->attributes = $body['response'];
            // print_r($body); die;
        }

        return $this->renderPartial('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->request('DELETE', 'cara-keluar/delete',[
                'query' => ['id' => $id ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/carakeluar.pdf";
        try {
            $response = $this->_restMaster->get('cara-keluar/cetak-pdf?' . http_build_query($yiiRestfulParams),[
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/Master Cara Keluar.xlsx";

        try {
            $response = $this->_restMaster->get('cara-keluar/export-excel?'.http_build_query($yiiRestfulParams), [
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

    public function actionGetCaraKeluarKode()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'cara-keluar/data-kode',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['carakeluar_kode'], 'text' => $value['carakeluar_kode']];
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

    public function actionGetCaraKeluarNama()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'cara-keluar/data-nama',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['carakeluar_nama'], 'text' => $value['carakeluar_nama']];
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

    public function actionGetCaraKeluarNamalain()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'cara-keluar/data-namalain',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['carakeluar_namalain'], 'text' => $value['carakeluar_namalain']];
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

    private function getRequest()
    {
        $response = $this->_restMaster->request('GET', 'cara-keluar/get-request');
        $body = json_decode($response->getBody(), true);

        return $body['response'];
    }

}
