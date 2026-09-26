<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * @date   : 2020-12-14
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class CronSetPoObatExpiredController extends Controller {
    public function actionIndex() {
        try {
            $_restPengadaan = Yii::$app->docoRest->pengadaan;
            $request = $_restPengadaan->get('allow/set-po-expired', [
                'form_params' => [
                    'type_po' => DocoConstants::JENIS_OBAT
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
