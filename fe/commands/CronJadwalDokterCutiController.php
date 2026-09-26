<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronJadwalDokterCutiController extends Controller
{
    public function actionIndex()
    {
        try {
            // Cron disable jadwal dokter by jadwal cuti
            $restMaster = Yii::$app->docoRest->master;
            $requestDisable = $restMaster->get('jadwal-dokter/disable-jadwal-dokter-cuti');
            $response = json_decode($requestDisable->getBody(), true);

            // Cron enable jadwal dokter by jadwal cuti
            $restMaster = Yii::$app->docoRest->master;
            $requestEnable = $restMaster->get('jadwal-dokter/enable-jadwal-dokter-cuti');
            $response = json_decode($requestEnable->getBody(), true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
