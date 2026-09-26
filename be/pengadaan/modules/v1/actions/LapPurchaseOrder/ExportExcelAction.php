<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPurchaseOrder;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanAllPOView;
use Doco\components\DocoRestActiveFilter;

class ExportExcelAction extends Action {
    public function run() {
        return Yii::$app->docoPlugin->execute('export_laporan_purchase_order');
    }
}
