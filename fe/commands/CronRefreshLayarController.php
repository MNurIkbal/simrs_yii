<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronRefreshLayarController extends Controller
{
    public function actionIndex()
    {
        try {
            $restAntrian = Yii::$app->docoRest->antrian;
            $request = $restAntrian->get('allow-antrian/refresh-layar');
            $response = json_decode($request->getBody(), true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
