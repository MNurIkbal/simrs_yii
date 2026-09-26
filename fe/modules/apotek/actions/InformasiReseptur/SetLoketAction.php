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

class SetLoketAction extends Action {
    // clone dari pendaftaran untuk pemilihan loket apotek : ali.padilah@docotel.com
    // clone action: PilihLoketAction, PanggilAntrianAction, dan SetLoketAction
    public function run() {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $post = Yii::$app->request->post();
        try {
            $loket_id = $post;
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $params = [
                'loginpemakai_id'=>$loginpemakai_id,
                'loket_id'=>$loket_id,
            ];
            $request = Yii::$app->docoRest->apotek->get('inf-reseptur/set-loket', [
                'form_params' => $params
            ]);
            $body = json_decode($request->getBody(), TRUE);
            $response = $body['response'];

            // use for all antrian biar engga perlu lagi hit backend, expired mengikuti active_loket
            $session->set('active_loket', [$loginpemakai_id=>$response]);

            return DocoHelpers::response('Loket berhasil di set.', 200);
        } catch (Exception $e) {
             return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}