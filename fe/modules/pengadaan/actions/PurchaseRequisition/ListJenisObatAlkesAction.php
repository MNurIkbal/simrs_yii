<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use app\modules\pengadaan\models\RecommendationOrderForm;
use GuzzleHttp\Exception\RequestException;

class ListJenisObatAlkesAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $is_consignment = $request->get('is_consignment', false);
        $model = new RecommendationOrderForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $jenisobatalkes = $this->controller->getListJenisObatAlkes($is_consignment);

        return $this->controller->renderPartial('list-jenis-obat-alkes', get_defined_vars());
    }
}