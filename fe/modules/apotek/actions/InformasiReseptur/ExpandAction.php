<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class ExpandAction extends Action {
    public function run($id, $noresep, $reseptur_id = null, $status_reseptur_id) {
        $getResepturDetail = Yii::$app->docoRest->apotek->get('inf-reseptur/get-expand-data',[
                                'query' => [
                                    'reseptur_id' => $reseptur_id,
                                    'nomor' => DocoHelpers::decrypt($noresep),
                                    'status_reseptur_id' => $status_reseptur_id
                                ]
                            ]);
        $detail = json_decode($getResepturDetail->getBody(),true);
        $data_racikan = count($detail['response']['racikan_freetext']) > 0 ? $detail['response']['racikan_freetext'] : [];
        $detail_resep = count($detail['response']['detail_resep']) > 0 ? $detail['response']['detail_resep'] : [];

        return $this->controller->renderAjax('expand', get_defined_vars());
    }
}
