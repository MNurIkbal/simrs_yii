<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
// use app\modules\v1\models\Pasien;
use app\modules\v1\models\InformasiPemakaianBarang;

class InfPemakaianBarangController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InformasiPemakaianBarang';

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
        // unset($actions['view']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $model = new InformasiPemakaianBarang;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pemakaianbarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pemakaianbarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pemakaianbarang']);
                $between = true;
            }
        }
        if($between) {
            $query->andWhere(['between', 'tgl_pemakaianbarang', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}