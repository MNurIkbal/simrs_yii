<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-12 11:00
 * @Last Modified by:   Naufal
 * @Description: controller untuk master Buat Janji Poliklinik (Pendaftaran) 
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\JanjiPoli;

class JanjiPoliController extends \Doco\components\DocoActiveController
{

	public $modelClass = 'app\modules\v1\models\JanjiPoli';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
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
        $_GET['expand'] = $request->get('expand', 'pegawai_m, ruangan_m');
        $model = new JanjiPoli;
        $query = $model::find()
            ->joinWith(['pegawai' => function($query){
                $query->select(['pegawai_m.nama_pegawai','pegawai_m.pegawai_id']);
            }])
              ->joinWith(['ruangan' => function($query){
               $query->select(['ruangan_m.ruangan_id','ruangan_m.ruangan_nama']);
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
