<?php
/**
 * @author: arief saputra
 * @description: master layar antrian
**/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\LoketForm;
use app\modules\master\models\LoketMpForm;
use GuzzleHttp\Exception\RequestException;

class LoketController extends DocoController
{
    protected $_title = 'Master Loket';
    protected $_module = 'loket/';
    protected $_restMaster;
    protected $allowAction = [
        'add-item-konfig',
        'reset-loket'
    ];

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
        $status = $this->_status;
        $title = $this->_title;
        $dataDropdown = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        $api = [];

        try {
            $responseRequest = $this->_restMaster->get('allow/get-for-loket');
            $body = json_decode($responseRequest->getBody(),true);
            $jenis_antrian = isset($body['response']['data_jenis_antrian']) 
                                ? $body['response']['data_jenis_antrian'] : [];
            $KonfigantrianRequest = $this->_restMaster->get('allow/get-group-konfig',[
                'query' => [
                ]
            ]);
            $body = json_decode($KonfigantrianRequest->getBody(),TRUE);
            $dataKonfig = $body['response'];

            foreach($dataKonfig as $key => $value) {
                $label = $key . " (" . implode(", ", array_unique(array_values($value))) . ")";
                $id = json_encode(array_keys($value));
                $api [$id] = $label;
            }
        } catch (RequestException $e) {
            $jenis_antrian = [];
            $api = [];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData($is_flag = false)
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->post());
        $filter['start'] = $request->post('start');
        $filter['length'] = $request->post('length');
        $post = $request->post();
        $response = $this->_restMaster->request('GET', 'loket/',[
                        'query' => $filter
                    ]);
        $row = [];
        $body = json_decode($response->getBody(),true);
        $no = $request->post('start',0);
        foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['loket_id']);
            $value['primary'] = $primaryKey;
            unset($value['loket_id']);

            $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif';
            if ($is_flag) {
                $value['aksi'] = Html::a(
                        "<i class='fa fa-pencil'></i>",Url::to([$this->_module .'update','id' => $primaryKey]), [
                        'style' => 'margin-right:5px;padding-left:7px !important;',
                        'class' => 'btn btn-primary btn-xs data-update',
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Ubah'),
                    ]
                );
            }

            $value['rowNum'] = $no;
            $row[$key] = $value;
        }
        $return = [
            'data' => $row,
            'draw' => $request->post('draw'),
            'recordsTotal' => $body['response']['count'],
            'recordsFiltered' => $body['response']['count']
        ];
        return DocoHelpers::response($return);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new loketForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('loket/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        $status = $this->_status;
        $request = Yii::$app->request;

        $session = Yii::$app->session;
        $title = 'Tambah Loket';
        $model = new LoketForm;
        if ($request->post()) {
            $model->load($request->post());
            $model->ruangan_id = empty($request->post()['LoketForm']['ruangan_id']) ? null : $request->post()['LoketForm']['ruangan_id'];
            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'loket/create',[
                                    'form_params' => $model->attributes
                            ]);

                $response = json_decode($response->getBody(),true);

                return DocoHelpers::response($response,false,'LoketForm');
            } else {
                return DocoHelpers::response($model->errors,422,'LoketForm');
            }
        } else {
            $responseRequest = $this->_restMaster->get('allow/get-for-loket');
            $body = json_decode($responseRequest->getBody(),TRUE);
            $AllData = $body['response'];
            $objAllData = json_encode($AllData);
            $val_konfig = [];
            $ddljenis_antrian = $AllData['data_jenis_antrian'] ? $AllData['data_jenis_antrian'] : [];
            $ddlruangan_id = $AllData['data_ruangan_farmasi'] ? $AllData['data_ruangan_farmasi'] : [];
            $idEncyrpt = 0;
            $isUpdate = 0;
            $is_keteranganpasien = false; //prevent error tmp
            return $this->render('form',get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new loketForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status;

        if ($request->post()) {
            $model->load($request->post());
            $model->is_active = $request->post()['LoketForm']['is_active'];
            $model->ruangan_id = empty($request->post()['LoketForm']['ruangan_id']) ? null : $request->post()['LoketForm']['ruangan_id'];
            if ($model->validate()) {

                $response = $this->helper->guzzleExec($this->_restMaster, [
                    'url' => 'loket/update',
                    'method' => 'post'
                ], [
                    'query' => [
                        'id' => $id
                    ],
                    'form_params' => $model->attributes
                ], true);

                if(isset($response)) {
                    if($response['httpStatusCode'] == 400) {
                        return DocoHelpers::macroResponseJson(400, $response['message']);
                    }
                    return DocoHelpers::macroResponseJson(200, $response['message']);
                }
            } else {
                return DocoHelpers::response($model->errors,422,'LoketForm');
            }
        } else {
            // $response = $this->_restMaster->get('konfig-system/get-konfig-system');
            // $body = json_decode($response->getBody(), TRUE);
            // $is_keteranganpasien = $body['response']['is_keteranganpasien'] ? true : false;
            $is_keteranganpasien = false;
            $response = $this->_restMaster->get('loket/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes['loket'];
            $model->ruangan_id = empty($attributes['loket']['ruangan_id']) ? NULL : $attributes['loket']['ruangan_id'];
            $idEncyrpt = DocoHelpers::encrypt($id);
            $model->is_active = $attributes['loket']['is_active'];
            $ddljenis_antrian = $attributes['option'];
            $ddlruangan_id = $attributes['ruangan'];
            $val_konfig = DocoHelpers::encrypt(json_encode($attributes['val_konfig']));
            $isUpdate = 1;
            return $this->render('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        $responseBackend = $this->helper->guzzleExec($this->_restMaster, [
            'url' => 'loket/delete',
            'method' => 'delete'
        ], [
            'query' => [
                'id' => $id
            ]
        ], true);
        if(isset($responseBackend)) {
            return DocoHelpers::macroResponseJson($responseBackend['httpStatusCode'], $responseBackend['message']);
        }
    }

    public function actionChangeStatus(){
        $request = Yii::$app->request;
        $model = new LoketForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $post = $request->post();

        if ($post) {
            $id = DocoHelpers::decrypt($post['id']);
            $model->load($request->post());
            $response = $this->_restMaster->put('loket/update?id='.$id, [
                'form_params' => ['is_active' => $post['is_active']]
            ]);

            return DocoHelpers::responseJsonString($response->getBody(), $formName);
        }
    }

    public function actionListKonfigantrian() {
        $request = Yii::$app->request;
        $post = $request->post();
        $jenisantrian_id = $post['depdrop_parents'][0];

        $KonfigantrianRequest = $this->_restMaster->get('allow/get-konfigantrian-by-jenis?jenisantrian_id='.$jenisantrian_id);
        $body = json_decode($KonfigantrianRequest->getBody(),TRUE);
        $dataKonfig = $body['response'];

        $out = [];
        foreach($dataKonfig as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionListLayarantrian($id = null) {
        $request = Yii::$app->request;
        $post = $request->post();
        $jenisantrian_id = $post['depdrop_parents'][0];

        $LayarantrianRequest = $this->_restMaster->get('allow/get-layarantrian-by-jenis?jenisantrian_id='.$jenisantrian_id);
        $body = json_decode($LayarantrianRequest->getBody(),TRUE);
        $dataKonfig = $body['response'];

        $out = [];
        foreach($dataKonfig as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>$id]);
        return;
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'loket/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/loket.xlsx";
        if(!isset($yiiRestfulParams['advanced-filter']['loket_nama']))
        {
            $yiiRestfulParams['advanced-filter']['loket_nama'] = Yii::$app->docoVars->workspace("loket_id");
        }    
        $response = $this->_restMaster->get($url, ['save_to' => $path]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::downloadFile($path, true);
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/loket.pdf";
        $response = $this->_restMaster->get('loket/cetak-pdf?'.http_build_query($yiiRestfulParams),[
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::downloadPdf($response,$path);
    }

    public function actionAddItemKonfig($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $compare = [];
        if ($id) {
            $id = DocoHelpers::decrypt($id);
            $compare = json_decode($id);
        }

        $post = $request->post();
        $dataKonfig = [];
        if (isset($post['jenisantrian_id'])) {
            $KonfigantrianRequest = $this->_restMaster->get('allow/get-group-konfig',[
                'query' => [
                    'jenisantrian_id' => $post['jenisantrian_id']
                ]
            ]);
            $body = json_decode($KonfigantrianRequest->getBody(),TRUE);
            $dataKonfig = $body['response'];
        }

        return DocoHelpers::response(['output'=>$dataKonfig]);
    }

    public function actionResetLoket()
    {
        $loginpemakai_id = Yii::$app->docoVars->user("id");

        $resetLoket = $this->_restMaster->get('allow/unset-loket?loginpemakai_id='.$loginpemakai_id);
        $body = json_decode($resetLoket->getBody(),TRUE);
        $data = $body['response'];
        
        return DocoHelpers::response(['output'=>$body]);
    }
}
