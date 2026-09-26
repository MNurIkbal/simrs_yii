<?php


namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\dcms\models\ReportForm;
use Doco\dcms\models\ReportConfigForm;

class ManajemenLaporanController extends DocoController
{
    protected $_module = 'dcms/manajemen-laporan/';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    public function actions()
    {
        return [
            'get-data-dokumen' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => Yii::$app->docoRest->dcms,
                'serviceAction' => 'report/get-dokumen',
                'data_name' => [
                    'nama_doc',
                ],
                'keyField' => 'id'
            ]
        ];
    }

    public function actionIndex()
    {
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

        $target = "report/index?".http_build_query($yiiRestfulParams);

        try {
            $response = Yii::$app->docoRest->dcms->get($target, [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['id']);
                $value['primary'] = $primaryKey;
                unset($value['id']);
                $value['rowNum'] = $no;
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

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Tambah');
        $modelReport = new ReportForm;
        $modelReportConfig = new ReportConfigForm;

        if($request->post()){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $modelReport->load($request->post());
            $modelReportConfig->load($request->post());
            $request = Yii::$app->docoRest->dcms->post('report/save-config',[
                'json'=>[
                    'report' => $modelReport->attributes,
                    'config' => $modelReportConfig->attributes
                ]
            ]);

            $response = json_decode($request->getBody(),true);

            return DocoHelpers::response($response,false,true);
        }

        $listRenderMode = ['full'=>'Full Feature','pdf'=> 'PDF Only'];
        $is_update = 0;
        $id = null;
        $rest = Yii::$app->docoRest->dcms->get('report/get-dokumen');
        $body = json_decode($rest->getBody());
        $listDokumen = isset($body->response->data) ? ArrayHelper::map($body->response->data,'docmapping_id','nama_dokumen') : [];
        
        return $this->renderAjax('form',get_defined_vars());
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Data');
        $modelReport = new ReportForm;
        $modelReportConfig = new ReportConfigForm;
        $status = $this->_status; 
        $options = $this->_options;
        // $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $modelReport->load($request->post());
            $modelReportConfig->load($request->post());
            $request = Yii::$app->docoRest->dcms->post('report/update-config?id='.$id,[
                'json'=>[
                    'report' => $modelReport->attributes,
                    'config' => $modelReportConfig->attributes
                ]
            ]);

            $response = json_decode($request->getBody(),true);

            return DocoHelpers::response($response,false,true);
        } else {
            $listRenderMode = ['full'=>'Full Feature','pdf'=> 'PDF Only'];
            $listDokumen = [];
            $is_update = 1;

            $rest = Yii::$app->docoRest->dcms->get('report/get-dokumen');
            $body = json_decode($rest->getBody());
            $listDokumen = isset($body->response->data) ? ArrayHelper::map($body->response->data,'docmapping_id','nama_dokumen') : [];
            
            $request = Yii::$app->docoRest->dcms->get('report/get-config?id='.$id);
            $response = json_decode($request->getBody(),TRUE);

            $modelReport->attributes = ArrayHelper::getValue($response,'response.report',[]);
            $modelReportConfig->attributes = ArrayHelper::getValue($response,'response.report.config',[]);

            $modelReportConfig->docmapping_enabled = ($modelReportConfig->docmapping_enabled) ? 1 : 0;
            $modelReportConfig->show_in_viewer = ($modelReportConfig->show_in_viewer) ? 1 : 0;
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionSaveReport($id = null)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $modelReport = new ReportForm;
        $modelReportConfig = new ReportConfigForm;
        $modelReport->load($request->post());
        $modelReportConfig->load($request->post());
        $request = Yii::$app->docoRest->dcms->post('report/save-config?id='.$id,[
            'json'=>[
                'report' => $modelReport->attributes,
                'config' => $modelReportConfig->attributes
            ]
        ]);

        $response = json_decode($request->getBody());
        $code = ArrayHelper::getValue($response,'response.data.report.code','');
        return [
            'url'=>'/reports/designer?kode='.$code
        ];
    }
}