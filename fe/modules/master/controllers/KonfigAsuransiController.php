<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KonfigAsuransiForm;
use app\components\DHtml;

class KonfigAsuransiController extends DocoController
{
  protected $allowAction = ['*'];
  protected $title = "Konfigurasi Integrasi Asuransi";
  protected $_module = '/master/konfig-asuransi/';
  protected $restMaster;
  protected $restPenjaminAsuransi;


  public function init()
  {
    parent::init();
    $this->restMaster = Yii::$app->docoRest->master;
    $this->restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
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
    return $this->render('index', compact('title'));
  }

  public function actionGetData()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $payload = DocoDatatableHelper::advancedFilterParam();
    $response = $this->guzzleExec($this->restMaster, [
      'url' => "konfig-asuransi/index",
      'payload' => [
        'query' => $payload,
      ]
    ]);
    foreach ($response['data'] as $key => $value) {
      $isActive = ArrayHelper::getValue($value, 'is_active', false);
      $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['konfigasuransi_id']);
      $response['data'][$key]['is_active'] = $isActive ? 'Aktif' : 'Tidak Aktif';
    }
    $response['recordsTotal'] = $response['_meta']['totalCount'];
    $response['recordsFiltered'] = $response['_meta']['totalCount'];
    return $response;
  }

  public function actionCreate()
  {
    $title = DHtml::getTitleMenu();
    $title = !empty($title) ? $title : $this->title;
    $request = Yii::$app->request;
    $model = new KonfigAsuransiForm;
    $statusAktif = ['1' => Yii::t('fe', 'Aktif'), '0' => Yii::t('fe', 'Tidak aktif')];
    $model->is_active = true;
    $optionProvider = json_encode([]);
    $id = null;
    if ($request->post()) {
      $model->load(Yii::$app->request->post());
      if ($model->validate()) {
        $response = $this->restMaster->request('POST', 'konfig-asuransi/save', [
          'form_params' => $model->attributes
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
      } else {
        $errors = DocoHelpers::parseError($model->errors, 'KonfigAsuransiForm');
        return DocoHelpers::response([
          'response' => [
            'data' => $errors
          ]
        ], 422);
      }
    }
    return $this->render('form', compact('title', 'statusAktif', 'model', 'optionProvider', 'id'));
  }

  public function actionUpdate()
  {
    $title = DHtml::getTitleMenu();
    $title = !empty($title) ? $title : $this->title;
    $model = new KonfigAsuransiForm;
    $request = Yii::$app->request;
    $id = $request->get('id');
    $id = DocoHelpers::decrypt($id);
    $model->konfigasuransi_id = $id;
    $statusAktif = ['1' => Yii::t('fe', 'Aktif'), '0' => Yii::t('fe', 'Tidak aktif')];
    if ($request->post()) {
      $model->load(Yii::$app->request->post());
      if ($model->validate()) {
        $response = $this->restMaster->request('POST', 'konfig-asuransi/save?id=' . $id, [
          'form_params' => $model->attributes,
          'query' => [
            'id' => $model->konfigasuransi_id
          ]
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
      } else {
        $errors = DocoHelpers::parseError($model->errors, 'KonfigAsuransiForm');
        return DocoHelpers::response([
          'response' => [
            'data' => $errors
          ]
        ], 422);
      }
    } else {
      $response = $this->guzzleExec($this->restMaster, [
        'url' => "konfig-asuransi/get-data",
        'payload' => [
          'query' => [
            'id' => $id,
          ],
        ]
      ]);
      if ($response && isset($response['data'])) {
        $model->attributes = $response['data'];
        $model->is_active = $model->is_active ? 1 : 0;
        $optionProvider = [
          'id' => $model->provider_id,
          'text' => $model->provider_code,
        ];
        $optionProvider = json_encode($optionProvider);
      }
    }

    return $this->render('form', compact('title', 'statusAktif', 'model', 'optionProvider', 'id'));
  }

  public function actionDelete($id)
  {
    $id = DocoHelpers::decrypt($id);
    try {
      $response = $this->restMaster->request('DELETE', 'konfig-asuransi/delete', [
        'query' => ['id' => $id]
      ]);
      $response = json_decode($response->getBody(), true);
      $response['response'] = [
        'title' => 'Proses Berhasil !',
        'text' => 'Data berhasil dihapus'
      ];
      return DocoHelpers::response($response);
    } catch (RequestException $e) {
      return DocoHelpers::response(['message' => $e->getMessage()], 500);
    } catch (\Exception $e) {
      return DocoHelpers::response(['message' => $e->getMessage()], 500);
    }
  }

  public function actionFilters()
  {
    $request = Yii::$app->request;
    $term = $request->get('term', null);
    $page = $request->get('page', 1);
    $response = $this->guzzleExec($this->restMaster, [
      'url' => 'konfig-asuransi/filters',
      'payload' => [
        'query' => [
          'term' => $term,
          'page' => $page,
        ]
      ],
    ]);
    return $this->responseJson(200, 'Data berhasil diambil!', $response);
  }

  public function actionCekKoneksi()
  {
    $request = Yii::$app->request;
    $model = new KonfigAsuransiForm;
    $model->scenario = 'cek-koneksi';
    if ($request->post()) {
      $model->load(Yii::$app->request->post());
      if ($model->validate()) {
        $response = $this->guzzleExec($this->restPenjaminAsuransi, [
          'url' => 'inf-integrasi-asuransi/cek-koneksi',
          'payload' => [
            'query' => [
              'provider' => $model->provider_code,
              'base_url' => $model->base_url,
              'auth' => $model->auth,
            ]
          ],
        ]);
        return DocoHelpers::response($response);
      } else {
        $errors = DocoHelpers::parseError($model->errors, 'KonfigAsuransiForm');
        return DocoHelpers::response([
          'response' => [
            'data' => $errors
          ]
        ], 422);
      }
    }
  }
}
