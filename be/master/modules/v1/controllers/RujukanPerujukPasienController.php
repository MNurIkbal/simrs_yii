<?php
//Author: Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\Perujuk;
use app\modules\v1\models\RujukanKeluar;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;


class RujukanPerujukPasienController extends \Doco\components\DocoActiveController
{
   	public $modelClass = '';

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


    public function actionGetAsalRujuk()
    {
      	$request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'asalrujukan_m');
        
        $model = new AsalRujukan;
        $query = $model::find()
             ->select([
                                'asalrujukan_m.asalrujukan_id',
                                'asalrujukan_m.asalrujukan_nama',
                                'asalrujukan_m.asalrujukan_namalainnya',
                                'asalrujukan_m.asalrujukan_institusi',
                                'asalrujukan_m.asalrujukan_kode',
                                'asalrujukan_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListAsalRujukan() 
    {
        $data = AsalRujukan::find()->where(['is_active' => 't'])->orderBy('asalrujukan_id');
        $items = ArrayHelper::map($data->all(), 'asalrujukan_id', 'asalrujukan_nama');

        return $items;
    }

    public function actionGetPerujuk()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'asalrujukan_m');
        $model = new Perujuk;
        $query = $model::find()
            ->joinWith(['asalRujukan' => function($query){
                $query->select(['asalrujukan_m.asalrujukan_nama','asalrujukan_m.asalrujukan_id']);
            }])
             ->where(['asalrujukan_m.is_deleted' => 'f']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetRujukanKeluar()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'asalrujukan_m');
        $model = new RujukanKeluar;
        $query = $model::find()
            ->joinWith(['asalRujukan' => function($query){
                $query->select(['asalrujukan_m.asalrujukan_nama','asalrujukan_m.asalrujukan_id']);
            }])->where(['asalrujukan_m.is_deleted' => 'f']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
