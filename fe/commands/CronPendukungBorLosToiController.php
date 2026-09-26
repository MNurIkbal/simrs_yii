<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronPendukungBorLosToiController extends Controller
{
    public function actionIndex()
    {

        try {

            //cron set antrian
            $_restRanap = Yii::$app->docoRest->ranap;
            $response = $_restRanap->get('allow-bor-los-toi/execute',[
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6NDksImFjY2Vzc190b2tlbiI6bnVsbCwiaXNfbW9iaWxlIjoxfQ.-5OD5nvosHn0zOvnvtWzJngol0Egd-vgsW0PnlYLTr0'
                ]
            ]);

            $response = json_decode($response->getBody(),true);

            echo json_encode($response) . PHP_EOL;
        } catch (RequestException $e) {
            echo "RequestException: " . $e->getMessage(). PHP_EOL;
        } catch (\Exception $e) {
            echo "Exception:" . $e->getMessage(). PHP_EOL;
        }
    }
}
