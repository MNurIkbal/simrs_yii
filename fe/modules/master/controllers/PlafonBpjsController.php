<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\PlafonBpjsForm;
use app\components\DHtml;

class PlafonBpjsController extends DocoController
{
    protected $allowAction = ['*'];
    protected $title = "Konfigurasi Plafon BPJS";
    public $module = '/master/plafon-bpjs/';
    protected $urlModule = 'plafon-bpjs/';
    protected $restMaster;

    public function init()
    {
        parent::init();
        $this->restMaster = Yii::$app->docoRest->master;
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
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->title;
        $module = $this->module;
        return $this->render('index', compact('title', 'module', 'dropDown'));
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->restMaster, [
            'url' => $this->urlModule."/index",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $primaryKey = DocoHelpers::encrypt($value['plafonbpjs_id']);
            $isActive = ArrayHelper::getValue($value, 'is_active', false);
            $response['data'][$key]['primary'] = $primaryKey;
            $response['data'][$key]['is_active'] = DocoHelpers::switchStatus($isActive, $primaryKey, 'change-status','Ya','Tidak');
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        $title = $id ? 'Edit Default Plafon' : 'Tambah Default Plafon';
        $model = new PlafonBpjsForm;
        $model->scenario = 'create';
        $model->plafonbpjs_id = $id;
        $statusAktif = ['1' => Yii::t('fe', 'Aktif'), '0' => Yii::t('fe', 'Tidak aktif')];
        $model->is_active = true;
        
        if ($request->post()) {
            return $this->handlePostRequest($model);
        }
        else {
            $disabled = false;
            if($id) {
                $disabled = true;
                $this->loadExistingData($model, $id);
            }
        }
        return $this->renderAjax('form', compact('title', 'statusAktif', 'model', 'instalasi', 'kelasPelayanan', 'id', 'disabled'));
    }

    public function actionFilters($type)
    {
        $response = $this->guzzleExec($this->restMaster, [
            'url' => $this->urlModule.'/filters',
            'method' => 'get',
            'payload' => [
                'query' => Yii::$app->request->get()
            ]
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response[$type]);
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        $response = $this->restMaster->request('POST', $this->urlModule.'/change-status?plafonbpjs_id=' . $id . '&is_active=' . $status);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }

    private function handlePostRequest($model)
    {
        $model->load(Yii::$app->request->post());
        $model->scenario = $model::SCENARIO_CREATE;
        if($model->plafonbpjs_id) {
            $model->scenario = 'default';
        }
        if ($model->validate()) {
            $response = $this->restMaster->request('POST', $this->urlModule.'/create', [
                'form_params' => $model->attributes
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } else {
            $errors = DocoHelpers::parseError($model->errors, 'PlafonBpjsForm');
            return DocoHelpers::response([
                'response' => [
                    'data' => $errors
                ]
            ], 422);
        }
    }

    private function loadExistingData($model, $id)
    {
        $response = $this->guzzleExec($this->restMaster, [
            'url' => $this->urlModule . "/get-data",
            'payload' => ['query' => ['id' => $id]],
        ]);
        if ($response && isset($response['data'])) {
            $model->attributes = $response['data'];
            $model->is_active = $model->is_active ? 1 : 0;
            if(!empty($model->list_ruangan_id) && !empty($model->plafon_ruangan)) {
                $model->is_ruangan = true;
            }
        }
    }

    public function actionGetRuangan($selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [
            'output' => [],
            'selected' => '',
        ];

        $parent_label = $ruanganId = null;

        if ($request->post('depdrop_parents')) {
            $parent_label = $request->post('depdrop_parents')[0];
        }

        if ($request->get('selected')) {
            $parent_label = $request->get('selected');
        }

        if ($request->get('ruangan_id')) {
            $ruanganIds = $request->get('ruangan_id');
            $result['selected'] = explode(',', $ruanganIds);
        }

        if (!empty($parent_label)) {
            try {
                $response = $this->restMaster->get('plafon-bpjs/list-ruangan', [
                    'query' => [
                        'parent_label' => $parent_label,
                        'ruangan_id' => $ruanganId
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $value) {
                    $result['output'][] = [
                        'id' => $value['ruangan_id'],
                        'name' => $value['ruangan_nama'],
                    ];
                }
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
            }
        }

        return $result;
    }

    public function actionDetail()
    {
        $title = 'Detail Ruangan';
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        $instalasiId = $request->get('instalasi_id');

        return $this->renderAjax('detail', compact('title', 'id', 'instalasiId'));
    }

    public function actionGetDataDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $request = Yii::$app->request;
        $plafonBpjsId = $request->get('plafonbpjs_id');
        if($plafonBpjsId) {
            $payload['plafonbpjs_id'] = $plafonBpjsId;
        }
        
        $response = $this->guzzleExec($this->restMaster, [
            'url' => $this->urlModule."/get-data-detail",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionCekDefaultPlafon()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $instalasiId = $request->get('instalasi_id');
        $kelasPelayananId = $request->get('kelaspelayanan_id');
        return $this->guzzleExec($this->restMaster, [
            'url' => $this->urlModule . "/get-default-plafon",
            'payload' => ['query' => ['instalasi_id' => $instalasiId, 'kelaspelayanan_id' => $kelasPelayananId]],
        ]);
    }
}
