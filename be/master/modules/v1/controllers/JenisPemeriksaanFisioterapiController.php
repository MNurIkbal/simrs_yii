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

use app\modules\v1\models\JenisPemeriksaanFisio;

class JenisPemeriksaanFisioterapiController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisPemeriksaanFisio';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['get-jenis-pemeriksaan'] = ['GET'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['delete']);

        return $actions;
    }

   public function actionGetJenisPemeriksaan(){
        $model = JenisPemeriksaanFisio::find(true)
        ->asArray()
        ->all();
        return $model;
   }
}
?>