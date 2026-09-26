<?php

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ListPemakaianAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $id = $request->get('id'); 
        $dataPemakaian = $request->get('data_pemakaian');
        $title = "Pemakaian Obat ".$dataPemakaian." Hari";

        return $this->controller->renderPartial('modal-list-pemakaian-obat', get_defined_vars());
    }
}
