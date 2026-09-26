<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        return Yii::$app->docoPlugin->execute($this->controller,'laporan_purchase_order_index');
    }
}
