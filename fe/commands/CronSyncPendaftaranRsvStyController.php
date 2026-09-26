<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronSyncPendaftaranRsvStyController extends Controller
{
    public function actionIndex()
    {
        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $request = $restPendaftaran->get('pendaftaran-reservasi/save-pendaftaran');
            $response = json_decode($request->getBody(), true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
