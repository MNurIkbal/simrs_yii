<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KonfigAsuransi;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

class KonfigAsuransiController extends \Doco\components\DocoActiveController
{

  public $modelClass = 'app\modules\v1\models\KonfigAsuransi';
  public function verbs()
  {
    $verbs = parent::verbs();
    $verbs["index"] = ["POST", "GET"];
    $verbs["update"] = ["POST", "PUT"];
    $verbs["create"] = ["POST"];
    return $verbs;
  }

  public function actions()
  {
    $actions = parent::actions();
    unset($actions['index']);
    unset($actions['delete']);
    unset($actions['view']);
    unset($actions['create']);
    unset($actions['update']);
    return $actions;
  }

  public function actionIndex()
  {
    try {
      $model = new KonfigAsuransi;
      $query = $model->find();
      if (isset($_GET['advanced-filter'])) {
        $advancedFilter = $_GET['advanced-filter'];
        if (isset($advancedFilter['provider_code']) && !empty($advancedFilter['provider_code'])) {
          $providerCode = $advancedFilter['provider_code'];
          $query->andWhere(['ILIKE', 'LOWER(provider_code)', strtolower($providerCode)]);
        }

        if (isset($advancedFilter['is_active']) && !empty($advancedFilter['is_active'])) {
          $isActive = $advancedFilter['is_active'];
          $isActive = $isActive == 1 ? true : false;
          $query->andWhere(['is_active' => $isActive]);
        }
      }

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
        'query' => $query->asArray(),
      ]);
    } catch (\yii\db\Exception $e) {
      return [
        'status' => 500,
        'message' => $e->getMessage()
      ];
    } catch (\Exception $e) {
      return [
        'status' => 500,
        'message' => $e->getMessage()
      ];
    }
  }

  public function actionFilters()
  {
    $request = Yii::$app->request;
    $result = [];
    $term = $request->get('term', null);
    $page = $request->get('page', 1);
    $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
    $result = Lookup::find()
      ->select(['lookup_id as id', 'lookup_name as text'])
      ->where(['lookup_type' => 'provider_asuransi', 'is_active' => true]);

    if (!empty($term)) {
      $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
    }

    $result->orderBy(['lookup_name' => SORT_ASC]);
    $result->limit(($limit + 1))->offset($limit * ($page - 1));
    return $result->asArray()->all();
  }

  public function actionSave()
  {
    $request = Yii::$app->request;
    $model = new KonfigAsuransi;
    $post = $request->post();
    $id = $request->get('id');
    try {
      if ($id) {
        $model = KonfigAsuransi::findOne($id);
      }
      $model->attributes = $post;
      if ($model->validate() && $model->save()) {
        $results = ['message' => 'Data Berhasil di simpan'];
      } else {
        $errors = DocoHelpers::parseError($model->errors, 'KonfigAsuransiForm');
        $results = [
          'data' => $errors,
          'status' => 422
        ];
      }
      return $results;
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

  public function actionGetData()
  {
    $request = Yii::$app->request;
    $id = $request->get('id', null);
    try {
      $model = KonfigAsuransi::findOne($id);
      if ($model) {
        $results = ['data' => $model];
      } else {
        $results = ['message' => 'Data Tidak Ditemukan'];
      }
      return $results;
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
    try {
      return (new KonfigAsuransi)->delete($id);
    } catch (\Exception $e) {
      \Yii::$app->response->statusCode = 500;
      return [
        'message' => $e->getMessage()
      ];
    }
  }

  public function actionGetPayload()
  {
    $request = Yii::$app->request;
    $konfigAsuransiId = $request->get('konfigasuransi_id', null);
    if ($konfigAsuransiId) {
      return KonfigAsuransi::findOne($konfigAsuransiId);
    }
    return [];
  }
}
