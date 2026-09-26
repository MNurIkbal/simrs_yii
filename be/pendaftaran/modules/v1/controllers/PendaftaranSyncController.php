<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\SyncPendaftaranTransaksi;

class PendaftaranSyncController extends \Doco\components\DocoActiveController
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
            $data_pendaftaran = $list_pendaftaranid = [];
            $data_sync = SyncPendaftaranTransaksi::find()->where(['is_sync'=>FALSE])->asArray()->all();

            if(is_array($data_sync) && count($data_sync)>0){
                foreach ($data_sync as $val_data_sync) {
                    $data_pendaftaran[] = $val_data_sync['additional_sync'];
                    $list_pendaftaranid[] = $val_data_sync['syncpendaftaran_id'];
                }
            }
            $restSync = Yii::$app->docoRest->sinkronisasi;

            if(count($data_pendaftaran)<1){
                return 'gagal';
            }
            $request = $restSync->post('sync/pendaftaran-many', [
                'json'=>$data_pendaftaran
            ]);

            $response = json_decode($request->getBody(),true);
            if($response == TRUE){
                $numberAffectedRows = SyncPendaftaranTransaksi::updateAll(['is_sync'=>TRUE],['IN','syncpendaftaran_id',$list_pendaftaranid]);
                $isSuccessSync = ($numberAffectedRows === count($data_pendaftaran));
            }
            return $isSuccessSync == TRUE?'sukses':'gagal';
        }catch(RequestException $e){
            return 'gagal';
        } catch(\Exception $e){
            return 'gagal';
        }
    }
}