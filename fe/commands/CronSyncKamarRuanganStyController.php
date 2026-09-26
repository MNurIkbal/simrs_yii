<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronSyncKamarRuanganStyController extends Controller
{
    public function actionIndex()
    {
        try {
            $restMaster = Yii::$app->docoRest->master;
            $request = $restMaster->get('inf-sinkronisasi/sync-kamar-ruangan');
            $response = json_decode($request->getBody(), true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
