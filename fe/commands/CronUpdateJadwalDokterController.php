<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronUpdateJadwalDokterController extends Controller
{
    public function actionIndex()
    {
        try {
            $restMaster = Yii::$app->docoRest->master;
            $request = $restMaster->get('jadwal-dokter/update-jadwal-dokter');
            $response = json_decode($request->getBody(), true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
