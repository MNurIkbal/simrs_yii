<?php

/**
 * @Author: Sigit
 * @Date:   2018-10-11 16:26:14
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronPendaftaranOnlineController extends Controller
{
    public function actionIndex()
    {
        try {
            $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $request = $_restPendaftaran->get('allow/auto-reject-pendaftaran-ol');

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }
}
