<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Suku;
use Doco\components\DocoHelpers;

class SukuController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Suku';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'suku_m');

        $model = new Suku;
        $query = $model::find()
             ->select([
                                'suku_m.suku_id',
                                'suku_m.suku_nama',
                                'suku_m.suku_namalainnya',
                                'suku_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDeleteSuku($suku_id)
    {
        try {
            $result = (new Suku)->delete($suku_id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

}
