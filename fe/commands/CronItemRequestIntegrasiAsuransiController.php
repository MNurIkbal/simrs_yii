<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronItemRequestIntegrasiAsuransiController extends Controller
{
    public function actionIndex()
    {
        try {
            $timeStart = microtime(true);
            $restPenjamin = Yii::$app->docoRest->penjaminasuransi;
            $requestDisable = $restPenjamin->post('inf-dashboard-integrasi/cron-integrasi-asuransi');
            $response = json_decode($requestDisable->getBody(), true);

            $timeEnd = microtime(true);
            $time = $timeEnd - $timeStart;
            $data = [
                'time_start' => $timeStart,
                'time_end' => $timeEnd,
                'time' => $time,
                'response' => $response
            ];
            echo json_encode($data) . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
