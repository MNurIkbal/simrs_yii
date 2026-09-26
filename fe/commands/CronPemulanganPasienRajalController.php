<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

/**
 * @Time Trigger = jam 00:00 setiap hari
 * @Command :
 * @Module : Rajal
 * @Desc: Cron Pemulangan Pasien Rajal Auto (status pasien Antrian Poliklinik, Antrian Kasir & Diperiksa)
 * per 08/12/2021 hanya digunakan untuk server RSU Adhyaksa
 *
 */
class CronPemulanganPasienRajalController extends Controller
{
    public function actionIndex()
    {
        try {
            $_restRajal = Yii::$app->docoRest->rajal;
            $request = $_restRajal->get('allow/cron-pemulangan-pasien', [
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiYjVkYzVmMGYwMGY5ZTY5Y2Y2YjNhMGJiZWRkOTNmNjdmMjhhYjQ1OWEzODc5M2JkMjgyNWQzZjc3MzI1OWVmNiIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.R51c6CzJdjuFOKG3X61fjUrw6YQ9ejej6uz7aVyarxo',
                    'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
                ],
            ]);

            $response = json_decode($request->getBody(), true);
            echo $response['response']['message'] . PHP_EOL;
        } catch (RequestException $e) {
            echo "Proses gagal " . $e->getMessage();
        } catch (\Exception $e) {
            echo "Proses gagal" . $e->getMessage();
        }
    }
}
