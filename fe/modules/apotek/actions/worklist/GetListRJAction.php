<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\worklist;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class GetListRJAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
        return $this->controller->getDataWorklist($instalasi_id);
    }
}