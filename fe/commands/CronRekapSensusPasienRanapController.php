<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronRekapSensusPasienRanapController extends Controller
{
    public function actionIndex()
    {
        try {
            $restRm = Yii::$app->docoRest->rm;
            $request = $restRm->get('allow/sync-sensus-pasien-ranap');
            $response = json_decode($request->getBody(), true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
