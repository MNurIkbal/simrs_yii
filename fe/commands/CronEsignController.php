<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronEsignController extends Controller
{
    public function actionGenerate()
    {
        try {
            $restRm= Yii::$app->docoRest->rm;
            $request = $restRm->get('esign/get-ungenerated-doc');

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }

    public function actionCheck()
    {
        try {
            $restRm= Yii::$app->docoRest->rm;
            $request = $restRm->get('tilaka/check-sign-all');

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }

    public function actionCheckS3()
    {
        try {
            $restRm= Yii::$app->docoRest->rm;
            $request = $restRm->get('tilaka/check-file-s3');

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }

    public function actionRenewStatus()
    {
        try {
            $restRm= Yii::$app->docoRest->rm;
            $request = $restRm->get('tilaka/renew-status');

            $response = json_decode($request->getBody(),true);

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal".$e->getMessage();
        }
    }
}
