<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ExpandAction extends Action {
    public function run($id) {
        $title  = \Yii::t('fe', 'Bahan Baku Produksi Obat');
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'inf-produksi-obat/get-data-expand-produksi',
            'payload' => [
                'form_params' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ],
        ]);
        
        $bahan_baku = count($response['bahan_baku']) > 0 ? $response['bahan_baku'] : [];
        $cekObat = count($response['ketersediaan']) > 0 ? $response['ketersediaan'] : [];

        return $this->controller->renderAjax('expand', get_defined_vars());
    }
}
