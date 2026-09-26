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

class SerahkanObatAction extends Action {
    public function run() {
        $nomor = Yii::$app->request->post('nomor');
        $ruangan_id = Yii::$app->request->post('ruangan_id');
        $instalasiasal_id = Yii::$app->request->post('instalasiasal_id');
        return $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'method' => 'post',
            'url' => 'inf-reseptur/serahkan-obat',
            'payload' => [
                'form_params' => [
                    'nomor' => $nomor,
                    'ruangan_id' => $ruangan_id,
                    'instalasiasal_id' => $instalasiasal_id
                ]
            ],
            'returnResponse' => true
        ]);
    }
}