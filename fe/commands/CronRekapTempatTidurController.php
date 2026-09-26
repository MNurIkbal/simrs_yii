<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

/**
 * @Author : Anggoro <tri.anggoro@docotel.com>
 * @Date : 21-Jun-2019

 * @Time Trigger = tanggal 1 setiap bulan
 * @Command :
 * @Module : Ranap
 * @Desc: Cron Rekap Tempat Tidur
 *
 */
class CronRekapTempatTidurController extends Controller
{
    public function actionIndex()
    {
        try {
            $_restRanap = Yii::$app->docoRest->ranap;
            $response = $_restRanap->get('allow/cron-rekap-tt', [
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6NDksImFjY2Vzc190b2tlbiI6bnVsbCwiaXNfbW9iaWxlIjoxfQ.-5OD5nvosHn0zOvnvtWzJngol0Egd-vgsW0PnlYLTr0'
                ]
            ]);

            $response = json_decode($response->getBody(), true);

            echo isset($response['response']['msg']) ? $response['response']['msg'].PHP_EOL : json_encode($response);

        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}

?>