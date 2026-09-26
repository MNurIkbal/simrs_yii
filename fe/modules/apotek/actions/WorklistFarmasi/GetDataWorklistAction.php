<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\WorklistFarmasi;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetDataWorklistAction extends Action {
    public function run() {
        try {
            $response = $this->controller->guzzleExec($this->controller->_restApotek, [
                'url' => 'worklist/get-data-worklist',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'identifier' => Yii::$app->request->get('identifier', null)
                    ]
                ]
            ]);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}