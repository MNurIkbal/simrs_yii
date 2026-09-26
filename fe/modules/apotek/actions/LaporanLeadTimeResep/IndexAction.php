<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanLeadTimeResep;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $list_ruangan = [];

        try {
            $response = $this->controller->_restApotek->get('allow/get-ruangan', [
                'query' => ['state' => false]
            ]);
            $response = json_decode($response->getBody(),true);
            $list_ruangan = ArrayHelper::map(ArrayHelper::getValue($response, 'response.data', []), 'ruangan_id', 'ruangan_nama');
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }

        return $this->controller->render('index', get_defined_vars());
    }
}
