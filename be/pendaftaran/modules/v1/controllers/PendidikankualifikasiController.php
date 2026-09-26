<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pendidikankualifikasi;

class PendidikankualifikasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendidikankualifikasi';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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
        $_GET['expand'] = $request->get('expand', 'kelompokpegawai_m,pendidikan_m');
        
        $model = new Pendidikankualifikasi;
        $query = $model::find()
            ->joinWith(['kelompokpegawai' => function($query){
                $query->from('kelompokpegawai_m');
            }])
            ->joinWith(['pendidikan' => function($query){
                $query->from('pendidikan_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}