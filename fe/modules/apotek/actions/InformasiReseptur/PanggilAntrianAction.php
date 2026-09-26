<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\Json;

class PanggilAntrianAction extends Action {
    // clone dari pendaftaran untuk pemilihan loket apotek : ali.padilah@docotel.com
    // clone action: PilihLoketAction, PanggilAntrianAction, dan SetLoketAction
    public function run($no_antrian = null, $extend_text = null, $loket = null, $loket_id = null, $antrian_id = null, $jenis_resep = null) {
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $session = Yii::$app->session;
        $cekLoket = empty($session->get('active_loket')[$loginpemakai_id])
                ? false
                : $session->get('active_loket')[$loginpemakai_id];

        // set ke display antrian
        $data_display = [
            'no_antrian' =>  $no_antrian,
            'jenis_resep' =>  $jenis_resep,
            'antrian_id' =>  $antrian_id,
            'no_loket' => empty($cekLoket['loket_nourut']) ? 0 : $cekLoket['loket_nourut'],
            'loket_id' => empty($cekLoket['loket_id']) ? 0 : $cekLoket['loket_id'],
            'extend_text' => $extend_text,
        ];


        $requests = Yii::$app->docoRest->apotek->get('inf-reseptur/panggil-antrian',['query'=>$data_display]);
        $response = json_decode($requests->getBody(), true);
        return DocoHelpers::response($response,false,true);

        // Yii::$app->redis->executeCommand('PUBLISH', [
        //     'channel' => 'display-antrian-dev',
        //     'message' => Json::encode(['data' => $data_display])
        // ]);

        // Yii::$app->redis->executeCommand('PUBLISH', [
        //     'channel' => 'display-antrian',
        //     'message' => Json::encode(['data' => $data_display])
        // ]);

        // $data_display["set_antrian"] = [
        //     'no_antrian' =>  $no_antrian,
        //     'loket_id' => empty($cekLoket['loket_id']) ? 0 : $cekLoket['loket_id'],
        // ];

        // Yii::$app->redis->executeCommand('PUBLISH', [
        //     'channel' => 'display-antrian',
        //     'message' => Json::encode(['data' => $data_display])
        // ]);

        // end set display antrian
    }
}