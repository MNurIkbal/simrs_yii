<?php

/**
 * @author : Ardi Pratama
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class ModalMultipleEtiketAction extends Action {
    public function run($nomor) {
        $title = 'Cetak multiple Etiket';
        $data_resep = [];
        $_requestDataResep = Yii::$app->docoRest->apotek->get('transaksi-resep/get-detail-etiket',[
            'query' => [
                'nomor' => DocoHelpers::decrypt($nomor)
            ]
        ]);
        $_responDataResep = json_decode($_requestDataResep->getBody(),true);
        $data_resep = $_responDataResep['response'];

        return $this->controller->renderAjax('_modal_cetak_multi_etiket', get_defined_vars());
    }
}