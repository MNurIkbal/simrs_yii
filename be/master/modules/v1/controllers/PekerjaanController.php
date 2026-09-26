<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pekerjaan;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;

class PekerjaanController extends \Doco\components\DocoActiveController
{

	public $modelClass = 'app\modules\v1\models\Pekerjaan';

	public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pekerjaan_m');
        
        $model = new Pekerjaan;
        $query = $model::find()
             ->select([
                                'pekerjaan_m.pekerjaan_id',
                                'pekerjaan_m.pekerjaan_nama',
                                'pekerjaan_m.pekerjaan_namalainnya',
                                'pekerjaan_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListPekerjaan() {
        $data = Pekerjaan::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $items = ArrayHelper::map($data->all(), 'pekerjaan_id', 'pekerjaan_nama');

        return $items;
    }

   

}
