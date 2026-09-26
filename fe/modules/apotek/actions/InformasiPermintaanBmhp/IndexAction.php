<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = "Informasi Permintaan BMHP";
        try {
            $request = Yii::$app->docoRest->apotek->get('allow/get-ruangan?state=0');
            $response = json_decode($request->getBody(), true);
            $ruangan = ArrayHelper::map($response['response']['data'], 'ruangan_id', 'ruangan_nama');
        } catch (\Exception $e) {
            $ruangan = [];
        }
        return $this->controller->render('index',get_defined_vars());
    }
}