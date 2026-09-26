<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronResetAntrianController extends Controller
{
    public function actionIndex()
    {

        try {

            //cron set antrian
            $_restAtrian = Yii::$app->docoRest->antrian;
            $responseAntrian = $_restAtrian->get('allow/set-antrian-number');

            $responseAntrian = json_decode($responseAntrian->getBody(),true);

            echo $responseAntrian['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
