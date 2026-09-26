<?php
/**
 * @Author: Naufal
 * @Date:   2018-01-09 13:30:49
 * @Last Modified by:   Naufal
 * @Description: controller untuk master Golongan Umur Pasien 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\GolonganUmur;
use Doco\components\DocoHelpers;

class GolonganUmurController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\GolonganUmur';

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
        $_GET['expand'] = $request->get('expand', 'golonganumur_m');

        $model = new GolonganUmur;
        $query = $model::find()
             ->select([
                                'golonganumur_m.golonganumur_id',
                                'golonganumur_m.golonganumur_nama',
                                'golonganumur_m.golonganumur_namalainnya',
                                'golonganumur_m.golonganumur_minimal',
                                'golonganumur_m.golonganumur_maksimal',
                                'golonganumur_m.is_active',
                      ]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
