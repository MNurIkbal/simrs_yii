<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoAccessRule;
use Doco\components\DocoJwtHttpBearerAuth;

use app\modules\v1\models\SyncsantoyusupV;
use app\modules\v1\models\SynceditsantoyusupV;


class DashboardIntegrationController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionGetDataTransaksi()
    {
        $request = Yii::$app->request;
        $type = $request->get('model');
        switch ($type) {
            case 'registrasi':
                return $this->getDataRegistrasi();
                break;
            case 'edit_regis':
                return $this->getDataEditRegistrasi();
                break;
            default:
                return '';
                break;
        }
    }

    public function actionDetailResponse()
    {
        $request = Yii::$app->request;
        $type = $request->get('model');
        switch ($type) {
            case 'registrasi':
                return $this->getDataDetailRegistrasi();
                break;
            case 'edit_regis':
                return $this->getDataDetailEditRegistrasi();
                break;
            default:
                return '';
                break;
        }
    }

    private function getDataRegistrasi()
    {
        $request = Yii::$app->request;
        $model = new SyncsantoyusupV;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getDataDetailRegistrasi()
    {
        $request = Yii::$app->request;
        return SyncsantoyusupV::find()
                ->select([
                    'additional_data','payload'
                ])
                ->where([
                    'id' => $request->get('id')
                ])
                ->asArray()
                ->one();
    }

    private function getDataEditRegistrasi()
    {
        $request = Yii::$app->request;
        $model = new SynceditsantoyusupV;
        $query = $model::find();

        if($request->get('state')) {
            $query->andWhere(['ILIKE', 'type', $request->get('state')]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getDataDetailEditRegistrasi()
    {
        $request = Yii::$app->request;
        return SynceditsantoyusupV::find()
                ->select([
                    'additional_data','payload'
                ])
                ->where([
                    'id' => $request->get('id')
                ])
                ->asArray()
                ->one();
    }
}