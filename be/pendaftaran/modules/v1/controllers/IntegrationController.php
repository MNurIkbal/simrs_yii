<?php

/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\SyncEditsantoyusupR;
use app\modules\v1\models\SatusehatInt;

use Doco\components\DocoAccessRule;
use Doco\components\DocoJwtHttpBearerAuth;

class IntegrationController extends DocoActiveController
{
    public $modelClass = '';

    const SAVE_STATE = 'save';

    public function verbs()
    {
        $verbs = parent::verbs();
        unset($verbs['authenticator']);
        unset($verbs['access']);
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['call-back-sync-ucup', 'callback-satusehat'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['call-back-sync-ucup', 'callback-satusehat'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    /**
     * Save response serconn santo yusup
     * 
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionCallBackSyncUcup()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $data = isset($post['data']) ? $post['data'] : null;

        if(empty($data)){
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Data pendaftaran kosong.'
            ]);
        }
        
        $pdftrnId =  isset($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null;
        $state = isset($data['state']) ? $data['state'] : null;

        if($state != self::SAVE_STATE) {
            $this->callBackEditSyncUcup($pdftrnId, $data);
        }

        $model = SyncsantoyusupR::find()
        ->where(['pendaftaran_id' => $pdftrnId])
        ->one();
        
        if($model) {
            $tmp = empty($model->count_sync) ? 1 : $model->count_sync;
            $model->is_sync = $data['status'];
            $model->additional_data = json_encode($data['message']);
            $model->last_modified_date = date("Y-m-d H:i:s");
            $model->count_sync = $tmp++;
            $model->save(false);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
    }

    private function callBackEditSyncUcup($pdftrnId, $data)
    {
        $model = SyncEditsantoyusupR::find()
        ->where(['pendaftaran_id' => $pdftrnId])
        ->one();
        
        if($model) {
            $tmp = empty($model->count_sync) ? 0 : 1;
            $model->is_sync = $data['status'];
            $model->additional_data = json_encode($data['message']);
            $model->last_modified_date = date("Y-m-d H:i:s");
            $model->count_sync = $tmp + 1;
            $model->state = $data['state'];
            $model->save(false);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionCallbackSatusehat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $data = isset($post['data']) ? $post['data'] : null;
             
        if (!empty($data)) {
            $idSercon = $post['uid'];
            $arrRes = json_decode($data,true);

            $model = SatusehatInt::find()
            ->where(['id_sync_sercon' => $idSercon])
            ->one();

            if($model) {
                $model->satusehat_id = ArrayHelper::getValue($arrRes, 'id');
                $model->sync_response = $data;
                $model->save();
            }
        }

        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
    }
}