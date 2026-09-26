<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanPurchaseRequisitionView;
use Doco\components\DocoRestActiveFilter;

class GetDataAction extends Action {
    public function run() {
        return Yii::$app->docoPlugin->execute('getdata_laporan_purchase_order');
    }
}
