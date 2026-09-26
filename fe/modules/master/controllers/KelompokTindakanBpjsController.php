<?php 

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
use app\modules\master\models\KelompokTindakanBpjsForm;

class KelompokTindakanBpjsController extends DocoController
{

    protected $_title = "Master Monitoring Pasien BPJS";
    protected $_module = '/master/KelompokTindakanBpjsForm';
    protected $_restMaster;

    protected $allowAction = [ '*' ];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        // $tindakaninacbgs = $this->getRequest();
        $inacbg = $this->getInacbgs();
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'kelompok-tindakan-bpjs/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['monitorbpjs_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $tmp_detail = $value['monitorbpjsdetail'];
                if (is_array($tmp_detail)) {
                    $detail = [];
                    foreach ($tmp_detail as $value1) {
                        $detail[] = $value1['groupinacbg_nama'];
                    }
                    $value['monitorbpjsdetail']=implode('<br>', $detail);
                }
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
            $title = 'Tambah Master Monitoring Pasien BPJS';
            $model = new KelompokTindakanBpjsForm;
            $request = Yii::$app->request;
            
            // $tindakaninacbgs = $this->getInacbgs();
            $inacbg = $this->getInacbgs();
            
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelompok-tindakan-bpjs/save-data',[
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'KelompokTindakanBpjsForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokTindakanBpjsForm');
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
        try{
            $title = 'Ubah Master Monitoring Pasien BPJS';
            $model = new KelompokTindakanBpjsForm;
            $id = DocoHelpers::decrypt($id);
            $request = Yii::$app->request;
            $post = $request->post();
            $model->load($post);
            // $tindakaninacbgs = $this->getInacbgs();
            $inacbg = $this->getInacbgs();
            if ($post) {
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelompok-tindakan-bpjs/edit-data?id=' . $id, [
                        'form_params' => $model->attributes,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response,false,'KelompokTindakanBpjsForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokTindakanBpjsForm');
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
                }
            } else {
                $response = $this->_restMaster->get('kelompok-tindakan-bpjs/view-data?id='.$id);
                $body = json_decode($response->getBody(), TRUE);
                $tmp_detail = $body['response']['monitorbpjsdetail'];
                    $detail = [];
                    if (is_array($tmp_detail)) {
                        foreach ($tmp_detail as $value1) {
                            $detail[] = $value1['groupinacbg_id'];
                        }
                    }
                $model->attributes = $body['response'];
                $model->groupinacbg_id = $detail;
            }

            return $this->renderPartial('form', get_defined_vars());
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->request('DELETE', 'kelompok-tindakan-bpjs/delete',[
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
        $path = Yii::getAlias("@download") . "/kelompok-tindakan-bpjs.pdf";
        try {
            $response = $this->_restMaster->get('kelompok-tindakan-bpjs/cetak-pdf?' . http_build_query($yiiRestfulParams),[
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
        $path = Yii::getAlias("@download") . "/Monitoring Pasien BPJS.xlsx";

        try {
            $response = $this->_restMaster->get('kelompok-tindakan-bpjs/export-excel?'.http_build_query($yiiRestfulParams), [
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

    public function actionGetTindakanNama()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'kelompok-tindakan-bpjs/data-nama',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['kelompoktindakan_nama'], 'text' => $value['kelompoktindakan_nama']];
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

    public function actionGetTindakanBPJS()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'kelompok-tindakan-bpjs/data-bpjs',[
                    'form_params'=>['term'=>$_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['groupinacbg_id'], 'text' => $value['groupinacbg_id']];
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
        $response = $this->_restMaster->request('GET', 'kelompok-tindakan-bpjs/get-request');
        $body = json_decode($response->getBody(), true);

        return $body['response'];
    }

    private function getInacbgs()
    {
        $response = $this->_restMaster->request('GET', 'group-ina-cbg/get-request');
        $body = json_decode($response->getBody(), true);

        return $body['response'];
    }

}
