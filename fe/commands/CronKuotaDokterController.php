<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronKuotaDokterController extends Controller
{
    public function actionIndex()
    {

        try {
            $antrian = Yii::$app->docoRest->antrian;
            $response = $antrian->get('allow/set-kuota-dokter');
            //cron pemulangan pasien
            // $_restRajal = Yii::$app->docoRest->rajal;
            // $responseRajal = $_restRajal->get('allow/set-pemulangan-pasien',[
            //     'headers' => [
            //         'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIn0.Vwrl4Era42ZZ3ZHszTHzUWQ5T4zKIeFaD5ifUlVfRHA'
            //     ]
            // ]);
            //

            // cron set batal pemesanan kamar
            // $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            // $responsePendaftaran = $_restPendaftaran->get('allow/set-batal-kamar',[
            //     'headers' => [
            //         'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIn0.Vwrl4Era42ZZ3ZHszTHzUWQ5T4zKIeFaD5ifUlVfRHA'
            //     ]
            // ]);
            //

            //cron set antrian
            $_restAtrian = Yii::$app->docoRest->antrian;
            $responseAntrian = $_restAtrian->get('allow/set-antrian-number');
            //

            $responseAntrian = json_decode($responseAntrian->getBody(),true);
            // $responsePendaftaran = json_decode($responsePendaftaran->getBody(),true);
            // $responseRajal = json_decode($responseRajal->getBody(),true);

            // // cron unset notif jadwal dokter
            // $master = Yii::$app->docoRest->master;
            // $responseNotif = $master->get('allow/unset-notif-dokter',[
            //     'headers' => [
            //         'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIn0.Vwrl4Era42ZZ3ZHszTHzUWQ5T4zKIeFaD5ifUlVfRHA'
            //     ]
            // ]);
            // $response = json_decode($response->getBody(),true);

            // echo $response['response']['message'] . PHP_EOL;
            echo $responseAntrian['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
