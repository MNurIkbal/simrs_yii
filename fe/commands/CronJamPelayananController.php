<?php

/**
 * @Author: Aris
 * @Date:   2019-08-01 13:19:14
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronJamPelayananController extends Controller
{

    public function actionIndex()
    {
        try {
            $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $request = $_restPendaftaran->get('allow/auto-reject-pendaftaran-online',[
            'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIn0.Vwrl4Era42ZZ3ZHszTHzUWQ5T4zKIeFaD5ifUlVfRHA'
                ]
            ]);

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }
}
