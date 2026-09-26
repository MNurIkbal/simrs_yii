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

class AlertHargaAction extends Action {
    public function run($id) {
        $title  = \Yii::t('fe', 'Bahan Baku Produksi Obat');

        return $this->controller->renderAjax('__modal_perubahan_harga', get_defined_vars());
    }
}
