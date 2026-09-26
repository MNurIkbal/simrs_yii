<?php

namespace Doco\master\controllers;

use Yii;
use yii\helpers\Html;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KategoriObatForm;
use app\components\DHtml;

class KategoriObatController extends DocoController
{
  protected $allowAction = ['*'];
  protected $title = "Kategori Obat";
  public $module = '/master/kategori-obat/';
  protected $urlModule = 'kategori-obat/';
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
    $filters = $this->actionFiltersIndex();
    $instalasi = ArrayHelper::getValue($filters, 'instalasi', []);
    $penjamin = ArrayHelper::getValue($filters, 'penjamin', []);
    
    return $this->render('index', get_defined_vars());
  }

  public function actionGetData()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $payload = DocoDatatableHelper::advancedFilterParam();
    
    // Keep pagination parameters for server-side pagination
    $page = ArrayHelper::getValue($payload, 'page', 1);
    $perPage = ArrayHelper::getValue($payload, 'per-page', 10);
    
    $response = $this->guzzleExec($this->restMaster, [
        'url' => $this->urlModule . "/index",
        'payload' => [
            'query' => $payload,
        ]
    ]);
    
    // Handle different response formats
    $data = [];
    $totalRecords = 0;
    $filteredRecords = 0;
    
    if (isset($response['data'])) {
        $data = $response['data'];
        $totalRecords = ArrayHelper::getValue($response, 'recordsTotal', count($data));
        $filteredRecords = ArrayHelper::getValue($response, 'recordsFiltered', count($data));
    } elseif (isset($response['message'])) {
        // Error response - return empty data
        return [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0
        ];
    } elseif (is_array($response) && !isset($response['data'])) {
        // If response is directly an array of data
        $data = $response;
        $totalRecords = count($data);
        $filteredRecords = count($data);
    } elseif (isset($response['body'])) {
        // If response has a body property
        $body = json_decode($response['body'], true);
        if (isset($body['data'])) {
            $data = $body['data'];
            $totalRecords = ArrayHelper::getValue($body, 'recordsTotal', count($data));
            $filteredRecords = ArrayHelper::getValue($body, 'recordsFiltered', count($data));
        } else {
            $data = $body;
            $totalRecords = count($data);
            $filteredRecords = count($data);
        }
    } else {
        return [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0
        ];
    }
    
    // Process the data for display (backend now returns grouped data with arrays)
    $processedData = [];
    foreach ($data as $value) {
        $id = ArrayHelper::getValue($value, 'restriction_obat_id', '');
        $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama', []);
        $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama', []);
        $isActive = ArrayHelper::getValue($value, 'is_active', false);
        
        // Convert arrays to strings for display
        $penjaminNamaString = is_array($penjaminNama) ? implode(', ', $penjaminNama) : $penjaminNama;
        $instalasiNamaString = is_array($instalasiNama) ? implode(', ', $instalasiNama) : $instalasiNama;
        
        // Format penjamin display
        $penjaminList = is_array($penjaminNama) ? $penjaminNama : explode(',', $penjaminNamaString);
        $penjaminDisplay = $penjaminNamaString;
        $penjaminTooltip = '';
        if(count($penjaminList) > 2) {
            $firstTwo = array_slice($penjaminList, 0, 2);
            $penjaminDisplay = implode(', ', $firstTwo) . ' ';
            $penjaminTooltip = implode(', ', $penjaminList);
            $penjaminDisplay .= '<strong><span class="ellipsis-tooltip" data-toggle="tooltip" title="' . Html::encode($penjaminTooltip) . '">...</span></strong>';
        }
        
        // Format instalasi display
        $instalasiList = is_array($instalasiNama) ? $instalasiNama : explode(',', $instalasiNamaString);
        $instalasiDisplay = $instalasiNamaString;
        $instalasiTooltip = '';
        if(count($instalasiList) > 2) {
            $firstTwo = array_slice($instalasiList, 0, 2);
            $instalasiDisplay = implode(', ', $firstTwo) . ' ';
            $instalasiTooltip = implode(', ', $instalasiList);
            $instalasiDisplay .= '<strong><span class="ellipsis-tooltip" data-toggle="tooltip" title="' . Html::encode($instalasiTooltip) . '">...</span></strong>';
        }
        
        $value['primary'] = DocoHelpers::encrypt($id);
        $value['penjamin_nama'] = $penjaminDisplay;
        $value['instalasi_nama'] = $instalasiDisplay;
        $value['is_active'] = $isActive ? 'Aktif' : 'Tidak Aktif';
        
        $processedData[] = $value;
    }
    
    return [
        'data' => $processedData,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords
    ];
  }


  public function actionCreate()
  {
    $request = Yii::$app->request;
    $id = $request->get('id');
    $postId = $request->post('id');
    $id = DocoHelpers::decrypt($id);
    $title = $id ? 'Edit Kategori Obat' : 'Tambah Kategori Obat';
    $model = new KategoriObatForm;
    $dropDown = $this->actionFilters();

    $instalasi = ArrayHelper::getValue($dropDown, 'instalasi', []);
    $instalasi = ArrayHelper::map($instalasi, 'id', 'text');

    $penjamin = ArrayHelper::getValue($dropDown, 'penjamin', []);
    $dataPenjaminSelect = $penjamin;

    if ($id) {
      $this->loadExistingData($model, $id);
    }


    if ($request->post()) {
      $model->load($request->post());
      
      if ($postId) {
        $model->restriction_obat_id = $postId;
        $model->scenario = KategoriObatForm::SCENARIO_UPDATE;
      }
      
      if (is_array($model->kategori)) {
        $model->kategori = isset($model->kategori[0]) ? $model->kategori[0] : null;
      }
      
      $detail = $request->post('detail', []);
      $model->detail_obat = $detail;
      
      if ($model->validate()) {
        $formData = $model->attributes;
        
        if ($postId) {
          $formData['restriction_obat_id'] = $postId;
        }

        $response = $this->restMaster->request('POST', $this->urlModule . '/save', [
          'form_params' => $formData
        ]);
        $result = json_decode($response->getBody(), true);
        return DocoHelpers::response($result);
      } else {
        $errorMessages = [];
        foreach ($model->errors as $errors) {
          $errorMessages = array_merge($errorMessages, $errors);
        }
        return DocoHelpers::response([
          'response' => [
            'title' => 'Proses Gagal!',
            'text' => $errorMessages[0]
          ]
        ], 422);
      }
    }

    return $this->renderAjax('form', compact('title', 'model', 'instalasi', 'dataPenjaminSelect', 'id'));
  }

  public function actionFiltersIndex()
  {
    return $this->guzzleExec($this->restMaster, [
      'url' => $this->urlModule . '/filters-index'
    ]);
  }

  public function actionFilters()
  {
    $response = $this->guzzleExec($this->restMaster, [
      'url' => $this->urlModule . '/filters'
    ]);
    return $response;
  }

  public function actionChangeStatus()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $request = Yii::$app->request;
    $id = $request->get('id');
    $status = $request->get('status');
    
    if (empty($id)) {
      return DocoHelpers::response([
        'response' => [
          'status' => 400,
          'title' => 'Error',
          'text' => 'ID tidak valid'
        ]
      ], 400);
    }
    
    try {
      $id = DocoHelpers::decrypt($id);
      
      $queryParams = 'restriction_obat_id=' . $id . '&is_active=' . $status;
      
      $response = $this->restMaster->request('POST', $this->urlModule . '/change-status?' . $queryParams);
      $result = json_decode($response->getBody(), true);
      
      return DocoHelpers::response($result);
    } catch (\Exception $e) {
      return DocoHelpers::response([
        'response' => [
          'status' => 500,
          'title' => 'Error',
          'text' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]
      ], 500);
    }
  }

  private function handlePostRequest($model)
  {
    $request = Yii::$app->request;
    $model->load($request->post());
    if ($model->validate()) {
      $postData = $request->post('KategoriObatForm');
      $detail = $request->post('detail', []);
      $model->kategori = isset($postData['kategori'][0]) ? $postData['kategori'][0] : null;
      $model->detail_obat = $detail;
      $response = $this->restMaster->request('POST', $this->urlModule . '/save', [
        'form_params' => $model->attributes
      ]);
      $response = json_decode($response->getBody(), true);

      $response = DocoHelpers::response($response);
      return DocoHelpers::response($response);
    } else {
      $errors = DocoHelpers::parseError($model->errors, 'KategoriObatForm');
      return DocoHelpers::response([
        'response' => [
          'data' => $errors
        ]
      ], 422);
    }
  }

  private function loadExistingData(&$model, $id)
  {
    $response = $this->guzzleExec($this->restMaster, [
      'url' => $this->urlModule . "/get-data",
      'payload' => ['query' => ['id' => $id]],
          ]);

      if ($response && isset($response['data'])) {
        $model->kategori = $response['data']['restriction_obat_nama'];
        
        $instalasi_ids = isset($response['mapping']['instalasi_ids']) ? array_values($response['mapping']['instalasi_ids']) : [];
        $model->instalasi_id = array_map('intval', $instalasi_ids);
        
        $penjamin_ids = isset($response['mapping']['penjamin_ids']) ? array_values($response['mapping']['penjamin_ids']) : [];
        $model->penjamin_id = array_map('intval', $penjamin_ids);
        
        $model->detail_obat = isset($response['mapping']['obatalkes_ids']) ? array_values($response['mapping']['obatalkes_ids']) : [];
      }
  }

  public function actionListObatAlkes()
  {
    $request = Yii::$app->request;
    $page = $request->get('page');
    $response = [];
    $limit = 10;
    $offset = ($page - 1) * 5;
    Yii::$app->response->format = Response::FORMAT_JSON;
    try {
      $result = $this->restMaster->get('kategori-obat/list-obat-alkes-infinity', [
        'query' => [
          'term' => $request->get('q'),
          'page' => $page,
          'group' => $request->get('group', null),
          'offset' => $offset,
          'limit' => $limit
        ]
      ]);

      $result = json_decode($result->getBody(), true);
      $data = isset($result['response']) ? $result['response'] : [];
      $response = [];
      foreach ($data as $key => $value) {
        $response[] = [
          'id' => $value['obatalkes_id'],
          'text' => $value['obatalkes_nama'],
          'datavalue' => $value
        ];
      }
    } catch (RequestException $e) {
      $response['message'] = $e->getMessage();
    }
    return DocoHelpers::response([
      'result' => $response,
      'pagination' => ['more' => !empty($data) ? true : false]
    ]);
  }

  public function actionGetObatDetails()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $request = Yii::$app->request;
    $obatIds = $request->post('obat_ids', []);

    if (empty($obatIds)) {
      return [
        'success' => false,
        'message' => 'No obat IDs provided'
      ];
    }

    try {
      $response = $this->restMaster->post($this->urlModule . '/get-obat-details', [
        'form_params' => [
          'obat_ids' => $obatIds
        ]
      ]);

      $result = json_decode($response->getBody(), true);
      
      $responseData = isset($result['response']['data']) ? $result['response']['data'] : 
                     (isset($result['data']) ? $result['data'] : []);
      
      return [
        'success' => true,
        'data' => $responseData
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  public function actionDelete($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    try {
      $id = DocoHelpers::decrypt($id);
      
      $queryParams = 'id=' . $id;
      
      $response = $this->restMaster->delete($this->urlModule . '/delete?' . $queryParams);
      $result = json_decode($response->getBody(), true);
      
      if (isset($result['success']) && $result['success']) {
        $return = [
          'success' => true,
          'message' => 'Data berhasil dihapus'
        ];
      } else {
        $return = [
          'success' => false,
          'message' => isset($result['message']) ? $result['message'] : 'Gagal menghapus data'
        ];
      }
      return $return;
    } catch (RequestException $e) {
      return [
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ];
    }
  }

  public function actionDeleteMultiple()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $request = Yii::$app->request;
    $ids = $request->post('ids', []);

    if (empty($ids)) {
      return [
        'success' => false,
        'message' => 'Tidak ada data yang dipilih untuk dihapus'
      ];
    }

    try {
      $decryptedIds = [];
      foreach ($ids as $id) {
        $decryptedIds[] = DocoHelpers::decrypt($id);
      }

      $response = $this->restMaster->request('POST', $this->urlModule . '/delete', [
        'form_params' => ['ids' => $decryptedIds]
      ]);

      $result = json_decode($response->getBody(), true);

      if (isset($result['success']) && $result['success']) {
        return [
          'success' => true,
          'message' => 'Data berhasil dihapus'
        ];
      } else {
        return [
          'success' => false,
          'message' => isset($result['message']) ? $result['message'] : 'Gagal menghapus data'
        ];
      }
    } catch (RequestException $e) {
      return [
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ];
    }
  }

  public function actionCheckActiveRestrictions()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $request = Yii::$app->request;
    $restrictionObatId = $request->get('restriction_obat_id');

    if (empty($restrictionObatId)) {
      return DocoHelpers::response([
        'success' => false,
        'message' => 'ID restriction obat wajib diisi',
        'active_count' => 0
      ]);
    }

    try {
      $id = DocoHelpers::decrypt($restrictionObatId);
      
      $response = $this->guzzleExec($this->restMaster, [
        'url' => $this->urlModule . 'check-active-restrictions',
        'payload' => [
          'query' => ['restriction_obat_id' => $id]
        ]
      ]);

      return DocoHelpers::response($response);
    } catch (\Exception $e) {
      return DocoHelpers::response([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'active_count' => 0
      ]);
    }
  }
}
