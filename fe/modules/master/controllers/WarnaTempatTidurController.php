<?php
    /*
    * @Author : Iqbal@docotel.com
    * @Date : 2018-08-31 10:56:40
    * @Last Modified by : 
    * @Last Modified time: 
    * @Desc :
    */

namespace Doco\master\controllers;

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

use app\modules\master\models\WarnaDokumenForm;
use app\modules\master\models\WarnaTempatTidurForm;

class WarnaTempatTidurController extends DocoController
{
    protected $_title = "Status Tempat Tidur";
    protected $_module = 'master/warna-tempat-tidur/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
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
        $model = new WarnaTempatTidurForm;
        $statuses = $this->_status; $options = $this->_options;
        // $kosong = [1=>'Kosong',0=>'Isi']; 
        $kosong = ['Kosong'=>'Kosong','Isi'=>'Isi']; 
        $status = ['Aktif'=>'Aktif','Tidak Aktif'=>'Tidak Aktif']; 
           
        return $this->render('index', get_defined_vars());
    }
    public function actionGetData()
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
            $response = $this->_restMaster->get('warna-tempat-tidur/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kettempattidur_id']);
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                $value['jenis_kamar'] = ($value['jenis_kamar'] != NULL) ? $value['jenis_kamar'] : '-';
                $nama_warna = ($value['kettempattidur_warna'] != NULL) ? $value['kettempattidur_warna'] : $value['kode_warna'];
                $value['kettempattidur_warna'] = 
                '<div class="col-md-8">
                    <button type="button" class="btn btn-xs btn-block" style="background:'.$value['kode_warna'].';">'.$nama_warna.'</button>
                </div>';
                unset($value['kettempattidur_id']);
                // $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Lihat').' '.\Yii::t('fe', $this->_title);
        $model = new WarnaTempatTidurForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('warna-tempat-tidur/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new WarnaTempatTidurForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->post('warna-tempat-tidur/create-wtt', [
                    'form_params' => $model->attributes
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $requests = $this->_restMaster->get('allow/get-list-jenis-kamar',['query' => ['type' => 'jenis_kamar' ]
                            ]);
            $response = json_decode($requests->getBody(), true);
            $jenis_kamar = $response['response'];
            $jenis_kamar = ArrayHelper::map($jenis_kamar, 'lookup_id', 'lookup_name');
            $model->is_kosong = 1;
            
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new WarnaTempatTidurForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            $model->kettempattidur_id = $id;
            $errors = DocoHelpers::parseError($model->errors, $formName);
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('warna-tempat-tidur/update-wtt?id='.$id, [
                        'query' => ['id' => $id ],
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $requests = $this->_restMaster->get('allow/get-api');
            $response = json_decode($requests->getBody(), true);
            $response = $response['response']['lookup'];
            $jenis_kamar = $response['jenis_kamar'];
            $jenis_kamar = ArrayHelper::map($jenis_kamar, 'lookup_id', 'lookup_name');

            $response = $this->_restMaster->get('warna-tempat-tidur/view-wtt?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_kosong = ($model->is_kosong == false) ? 0 : 1;
            
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->get('warna-tempat-tidur/delete-wtt',
                ['query'=>['id'=>$id]]
            );
            $response = json_decode($response->getBody(),true);

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
        $path = Yii::getAlias("@download") . "/status-tempat-tidur.pdf";
        try {
            $response = $this->_restMaster->get('warna-tempat-tidur/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('warna-tempat-tidur/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
           
            return DocoHelpers::downloadFile($url);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionListWarnaTempatTidur() {
        $request = Yii::$app->request;
        $post = $request->post();
        if(isset($post['depdrop_parents'][0])) {
            $kamarruangan_id = $post['depdrop_parents'][0];
            $PropinsiRequest = $this->_restMaster->get('warna-tempat-tidur/list-warna-tempat-tidur',
                ['query'=>['kamarruangan_id'=>$kamarruangan_id]
                ]);

            $body = json_decode($PropinsiRequest->getBody(),TRUE);
            $ddlKabupaten = $body['response'];
            
            $out = [];
            foreach($ddlKabupaten as $kab => $value) {
                $out[] = [
                    'id' => $kab,
                    'name' => $value
                ];
            }
            
            echo json_encode(['output'=>$out]);
            return;
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }
}
