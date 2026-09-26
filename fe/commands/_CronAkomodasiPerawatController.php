<?php

/**
 * Cron Akomodasi Keperawatn
 * set cron 0 0 * * * cd /var/www/sirs/frontend && php5.6 Yii cron-akomodasi-perawat
 * Keperluan untuk mengenerate semua akomodasi keperawatan untuk pasien yang masih di rawat
 */
namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronAkomodasiPerawatController extends Controller
{
    public function actionIndex()
    {
        try{
            $ranap = Yii::$app->docoRest->ranap;
            $responseKamar = $ranap->get('allow-akomodasi/',[
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIn0.Vwrl4Era42ZZ3ZHszTHzUWQ5T4zKIeFaD5ifUlVfRHA'
                ]
            ]);

            $responseKamar = json_decode($responseKamar->getBody(),true);
            echo $responseKamar['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}