<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class SearchNoAdjustmentAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('term', '');
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        try {
            $response = Yii::$app->docoRest->gudang->get(
                        'informasi-adjustment-obat-alkes/search-no-adjustment?term='.$term.'&ruangan_id='.$ruangan_id,
                        ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];

            $list = [];
            foreach ($data as $value) {
                $item = [
                    "id" => $value['no_adjusmen'],
                    "text" => $value['no_adjusmen']
                ];
                $list[] = $item;
            }

            return [
                'list' => $list
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}