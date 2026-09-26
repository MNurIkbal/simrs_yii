<?php
/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-09 13:30:49
 * @Last Modified by:   Naufal
 * @Description: controller untuk master Cara Masuk Pasien 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\CaraMasuk;
use Doco\components\DocoHelpers;

class CaraMasukController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\CaraMasuk';

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
        $_GET['expand'] = $request->get('expand', 'caramasuk_m');
        
        $model = new CaraMasuk;
        $query = $model::find()
             ->select([
                                'caramasuk_m.caramasuk_id',
                                'caramasuk_m.caramasuk_nama',
                                'caramasuk_m.caramasuk_namalainnya',
                                'caramasuk_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
