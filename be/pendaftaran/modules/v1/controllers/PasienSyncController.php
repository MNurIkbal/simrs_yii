<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\SyncPasien;

class PasienSyncController extends \Doco\components\DocoActiveController
{
	public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    // allow all method without authentication
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actionSaveBatch()
    {
        try{
            $data_pasien = $list_pasienid = [];
            $data_sync = SyncPasien::find()->where(['is_sync'=>FALSE])->asArray()->all();

            if(is_array($data_sync) && count($data_sync)>0){
                foreach ($data_sync as $val_data_sync) {
                    $data_pasien[] = $val_data_sync['additional_sync'];
                    $list_pasienid[] = $val_data_sync['syncpasien_id'];
                }
            }
            $restSync = Yii::$app->docoRest->sinkronisasi;

            if(count($data_pasien)<1){
                return 'gagal';
            }
            $request = $restSync->post('sync/pasien-many', [
                'json'=>$data_pasien
            ]);

            $response = json_decode($request->getBody(),true);
            if($response == TRUE){
                $numberAffectedRows = SyncPasien::updateAll(['is_sync'=>TRUE],['IN','syncpasien_id',$list_pasienid]);
                $isSuccessSync = ($numberAffectedRows === count($data_pasien));
            }
            return $isSuccessSync == TRUE?'sukses':'gagal';
        }catch(RequestException $e){
            return 'gagal';
        } catch(\Exception $e){
            return 'gagal';
        }
    }
}