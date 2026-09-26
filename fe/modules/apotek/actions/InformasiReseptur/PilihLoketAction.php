<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PilihLoketAction extends Action {
    // clone dari pendaftaran untuk pemilihan loket apotek : ali.padilah@docotel.com
    // clone action: PilihLoketAction, PanggilAntrianAction, dan SetLoketAction
    public function run($jenisantrian_id) {
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $requests = Yii::$app->docoRest->master->get('allow/get-loket?jenisantrian_id='.$jenisantrian_id.'&ruangan_id='.$ruangan_id);
        $response = json_decode($requests->getBody(), true);
        $listResponses = $response['response'];

        $title = Yii::t('fe', 'Pilih loket');

        return $this->controller->render('pilih-loket', get_defined_vars());
    }
}