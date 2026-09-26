<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@docotel.com)
 * @date   : 2021-07-22
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronPullingBaseCalcRecommendationOrderController extends Controller 
{
    public function actionIndex() 
    {
        try {
            $_restPengadaan = Yii::$app->docoRest->pengadaan;
            $request = $_restPengadaan->get('purchase-requisition/pull-base-calc-ro', [
            'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiYjVkYzVmMGYwMGY5ZTY5Y2Y2YjNhMGJiZWRkOTNmNjdmMjhhYjQ1OWEzODc5M2JkMjgyNWQzZjc3MzI1OWVmNiIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.R51c6CzJdjuFOKG3X61fjUrw6YQ9ejej6uz7aVyarxo',
                    'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
                ],
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
