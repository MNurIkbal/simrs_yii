<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class LogPerubahanAction extends Action {
    protected $_title = "Log Perubahan Resep";

    public function run() {
        $title = 'Log Perubahan Resep';
        $request = Yii::$app->request;
        $id = $request->get()['id'];
        $type = $request->get()['type'];
        $result = false;

        return $this->controller->renderAjax('_modal_log_perubahan', compact(
            'id',
            'type',
            'title',
            'result'
        ));
    }
}
