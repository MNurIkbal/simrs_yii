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

class GetDetailAction extends Action {
    public function run($identifier) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $response = Yii::$app->docoRest->apotek->get('worklist/detail', [
                'query' => [
                    'identifier' => $identifier
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return $body;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}