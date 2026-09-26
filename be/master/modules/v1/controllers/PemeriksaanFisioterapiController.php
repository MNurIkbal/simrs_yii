<?php

/**
* @author Andri Amirul (andri.amirul@sirs.co.id)
* A Product of PT Citraraya Nusatama
* Powered by Sirs
*/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\PemeriksaanFisio;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\TindakanFisioterapiView;

class PemeriksaanFisioterapiController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\PemeriksaanFisio';

    // Verbs
    public function verbs()
    {
        // Verbs parent
        $verbs = parent::verbs();
        $verbs['get-pemeriksaan'] = ['GET'];
        // Return verbs
        return $verbs;
    }

    // Actions
    public function actions()
    {
        // Actions parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);

        // Return actions
        return $actions;
    }

   public function actionGetPemeriksaan($jenispemeriksaanfisio_id = null){
        $model = TindakanFisioterapiView::find();
        if($jenispemeriksaanfisio_id){
            $model->where(['jenispemeriksaanfisio_id' => $jenispemeriksaanfisio_id]);
        }else{
            $model->select(['daftartindakan_id', 'pemeriksaanfisio_nama']);
        }
        return $model->asArray()->all();
   }
}
?>