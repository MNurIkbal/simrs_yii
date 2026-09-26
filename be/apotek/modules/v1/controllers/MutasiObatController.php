<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\entities\MutasiObat;

class MutasiObatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\MutasiObatRuangan';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionLangsung()
    {
        // getfromrequest
        $inputMutasi = Yii::$app->request->post('mutasi',null);
        $inputMutasiDetail = Yii::$app->request->post('mutasiDetail',null);
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $mutasi = (new MutasiObat)->loadMutasiLangsung($inputMutasi,$inputMutasiDetail);
            $return = $mutasi->saveLangsung();
            
            $transaction->commit();
            return ['message'=>'Berhasil','id'=> $return['id'],'nomor'=>$return['nomor']];
        }catch( \Exception $e){
            \Yii::$app->response->statusCode = 500;
            $transaction->rollback();
            return ['data'=>$e->getMessage()];
        }
    }
}