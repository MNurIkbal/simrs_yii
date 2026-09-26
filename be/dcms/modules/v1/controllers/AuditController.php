<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\AuditTrail;
use app\modules\v1\models\AuditLogged;

class AuditController extends DocoActiveController
{

    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionLog()
    {
        $model = new AuditLogged();

        $finder = $model->find();
        $finder->limit(10);

        $between = false;

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['action_tstamp_tx'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['action_tstamp_tx']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    $finder->andWhere(['between', 'action_tstamp_tx', $start, $end]);
                }
                unset($_GET['advanced-filter']['action_tstamp_tx']);
            }

            if (isset($_GET['advanced-filter']['action'])) {
                $finder->andWhere(['action' => trim($_GET['advanced-filter']['action'])]);
                unset($_GET['advanced-filter']['action']);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $finder);
        $dataProvider =  new ActiveDataProvider([
            'query' => $query,
        ]);

        return [
            'data' => $dataProvider->getModels(),
            '_meta' => [
                'totalCount' => $dataProvider->getTotalCount(),
                'pageCount' => $dataProvider->getPagination()->getPageCount(),
                'currentPage' => $dataProvider->getPagination()->getPage()
                    ? $dataProvider->getPagination()->getPage()
                    : 1,
                'perPage' => $dataProvider->getPagination()->getPageSize(),
            ]
        ];

    }

    public function actionUseract()
    {
        $model = new AuditTrail();

        $finder = new Query();
        $finder->limit(10);
        $finder->select('audit_trail_k.*, loginpemakai.nama_pegawai');
        $finder->from('audit_trail_k');
        $finder->leftJoin('loginpemakai_v loginpemakai', 'audit_trail_k.loginpemakai_id = loginpemakai.loginpemakai_id');

        $between = false;

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['stamp'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['stamp']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    $finder->andWhere(['between', 'stamp', $start, $end]);
                }
                unset($_GET['advanced-filter']['stamp']);
            }

            if (isset($_GET['advanced-filter']['action'])) {
                $finder->andWhere(['action' => trim($_GET['advanced-filter']['action'])]);
                unset($_GET['advanced-filter']['action']);
            }

            if (isset($_GET['advanced-filter']['loginpemakai.nama_pegawai'])) {
                $finder->andWhere(['ilike', 'loginpemakai.nama_pegawai', trim($_GET['advanced-filter']['loginpemakai.nama_pegawai'])]);
                unset($_GET['advanced-filter']['loginpemakai.nama_pegawai']);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $finder);
        $dataProvider =  new ActiveDataProvider([
            'query' => $query,
        ]);

        return [
            'data' => $dataProvider->getModels(),
            '_meta' => [
                'totalCount' => $dataProvider->getTotalCount(),
                'pageCount' => $dataProvider->getPagination()->getPageCount(),
                'currentPage' => $dataProvider->getPagination()->getPage()
                    ? $dataProvider->getPagination()->getPage()
                    : 1,
                'perPage' => $dataProvider->getPagination()->getPageSize(),
            ]
        ];
    }

    public function actionDetailUseract($id)
    {
        $model = AuditTrail::find();
        $model->where(['audit_trail_id' => $id]);
        $model->with('pemakai');
        $tmp = ArrayHelper::toArray($model->one());
        $result = $tmp;
        $result['detail'] = isset($tmp['detail']) && !is_null($tmp['detail']) ? json_decode($tmp['detail']) : (object) [];
        $result['messages'] = isset($tmp['messages']) && !is_null($tmp['messages']) ? json_decode($tmp['messages']) ?: (object)[] : (object) [];
        return $result;
    }

    public function actionDetailLog($id)
    {
        $model = AuditLogged::find();
        $model->where(['event_id' => $id]);
        $tmp = ArrayHelper::toArray($model->one());
        $result = $tmp;
        return $result;
    }
}