<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronAkomodasiKamarController extends Controller
{
    public function actionIndex()
    {
    	try{
            $master = Yii::$app->docoRest->master;
            $responseKamar = $master->get('allow/create-akomodasi-kamar',[
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