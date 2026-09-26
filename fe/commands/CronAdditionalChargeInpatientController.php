<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * @date   : 2021-02-21
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use GuzzleHttp\Exception\RequestException;

class CronAdditionalChargeInpatientController extends Controller {
    public function actionIndex() {
        try {
            $_restRanap = Yii::$app->docoRest->ranap;
            $request = $_restRanap->get('allow-additional-charge-inpatient/',[
            'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiYjVkYzVmMGYwMGY5ZTY5Y2Y2YjNhMGJiZWRkOTNmNjdmMjhhYjQ1OWEzODc5M2JkMjgyNWQzZjc3MzI1OWVmNiIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.R51c6CzJdjuFOKG3X61fjUrw6YQ9ejej6uz7aVyarxo',
                    'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
                ],
            
            ]);

            echo "Proses Selesai";
        } catch (RequestException $e) {
            echo "Proses gagal ".$e->getMessage().$e->getLine().$e->getFile();
        } catch (\Exception $e) {
            echo "Proses gagal ".$e->getMessage().$e->getLine().$e->getFile();
            // echo "Proses gagal".$e->getMessage();
        }
    }
}
