<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class InitController extends Controller
{
    public function actionIndex()
    {
        $ini = @parse_ini_file('config/env/.api', true);
        $no = 1;
        foreach ($ini as $key => $value) {
            $scheme = parse_url($value, PHP_URL_SCHEME) .'://';
            $scheme .= parse_url($value, PHP_URL_HOST);
            try {
                $curl = Yii::$app->docoRest->{$key};
                $default = [
                    'create-module' => 1
                ];
                if ($no == count($ini)) {
                    $default['flag'] = 1;
                }
                $response = $curl->request('GET', $scheme . '/site/config',[
                                    'query' => $default,
                            ]);
                echo "Service " . strtoupper($key) . " Berhasil di buat" . PHP_EOL;
            } catch(RequestException $e) {
                var_dump($e->getMessage());
                die();
                echo "Service " . strtoupper($key) . " Gagal di buat" . PHP_EOL;
                continue;
            } catch (\Exception $e) {
                echo "Service " . strtoupper($key) . " Gagalz di buat" . PHP_EOL;
                continue;
            }
            $no++;
        }
    }
}
