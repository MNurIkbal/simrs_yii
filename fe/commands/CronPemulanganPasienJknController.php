<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronPemulanganPasienJknController extends Controller
{
    public function actionIndex()
    {
        try {
            $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $request = $_restPendaftaran->get('api/auto-pulang-pasien-jkn');

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }
}
