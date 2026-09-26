<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\actions\KonfigObatRuangan;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $module = $this->controller->_module;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $getListRuangan = Yii::$app->docoRest->gudang->get('allow/get-list-instalasi-ruangan');
        $resRuangan = json_decode($getListRuangan->getBody(), true);
        $map_ins_ruangan = [];
        foreach ($resRuangan['response'] as $key => $value) {
            $map_ins_ruangan[] = [
                'ruangan_id' => $value['ruangan_id'],
                'instalasi_ruangan' => $value['instalasi_nama'] . " - " . $value['ruangan_nama']
            ];
        }

        $instalasi_ruangan = ArrayHelper::map($map_ins_ruangan, 'ruangan_id', 'instalasi_ruangan');

        return $this->controller->render('index', get_defined_vars());
    }
}
