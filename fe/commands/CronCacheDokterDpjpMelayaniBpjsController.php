<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronCacheDokterDpjpMelayaniBpjsController extends Controller
{
    const CACHE_KEY = 'dpjp-melayani';

    public function actionIndex()
    {
        try {
            $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $term = "";
            Yii::$app->cache->delete(self::CACHE_KEY);
            
            $response = $_restPendaftaran->post('allow-bpjs/referensi-dpjp', [
                'form_params' => [
                    'q' => $term,
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            $bridgeRes = $response['response'];
            if ($bridgeRes && $bridgeRes['metaData']['code'] == 200 ) {
                $list = $bridgeRes['response']['list'];
                if(!empty($list)) {
                    Yii::$app->cache->set(self::CACHE_KEY, $list, 43200); // 12 hours
                }
            }

            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
