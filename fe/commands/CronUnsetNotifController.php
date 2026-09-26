<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronUnsetNotifController extends Controller
{
    public function actionIndex()
    {

        try {
            $_restMaster = Yii::$app->docoRest->master;
            $response = $_restMaster->get('allow/unset-notif-dokter',[
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6NDksImFjY2Vzc190b2tlbiI6bnVsbCwiaXNfbW9iaWxlIjoxfQ.-5OD5nvosHn0zOvnvtWzJngol0Egd-vgsW0PnlYLTr0'
                ]
            ]);

            $response = json_decode($response->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
