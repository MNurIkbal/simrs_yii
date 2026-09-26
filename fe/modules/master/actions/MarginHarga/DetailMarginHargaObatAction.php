<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class DetailMarginHargaObatAction extends Action {
    public function run($id) {
        $request = Yii::$app->docoRest->master->request('GET', 'margin-harga/detail-margin-harga-obat?id='.DocoHelpers::decrypt($id));
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $this->controller->renderAjax('_detail', get_defined_vars());
    }
}