<?php

/**
 * @author Randy Vianda Putra
 * @todo All about notication
 * @copyright 8 November 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use Doco\components\DocoAccessRule;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoJwtHttpBearerAuth;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\Notifikasi;
use Doco\components\DocoHelpers;
use yii\filters\AccessControl;
use Doco\components\DocoNotification;
use app\modules\v1\models\NotifikasiComponent;

class NotifikasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JadwalDokter';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['list-notif-user'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['list-notif-user'],
        ];

        return $behaviors;
    }

    /**
    * @author Randy Vianda Putra
    * @todo get list all notif by loginmobile_id
    */
    public function actionListNotifUser($loginmobile_id)
    {
        $data = NotifikasiComponent::queryListNotifByUser($loginmobile_id);
        $response['data'] = $data;

        return $response;
    }

    /**
    * @author Randy Vianda Putra
    * @todo get list all notif by day for antrian
    */
    public function actionListNotifDay($hari)
    {
        $hari_id = DocoConstants::$look_hari[$hari];
        $data = NotifikasiComponent::queryListNotifByDay($hari_id);
        $data_notif['list_notification'] = $data;
        $mode = Yii::$app->params['mode'];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'display-notif-'.$mode,
            'message' => json_encode(['data' => $data_notif])
        ]);

        return $data;
    }

}
