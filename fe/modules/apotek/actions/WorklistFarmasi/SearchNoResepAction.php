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

class SearchNoResepAction extends Action {
    public function run() {
        try {
            $response = $this->controller->guzzleExec($this->controller->_restApotek, [
                'url' => 'worklist/get-list-nomor-resep',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'term' => Yii::$app->request->get('term', null)
                    ]
                ]
            ]);

            $list = [];
            foreach ($response as $resep) {
                $nomor = isset($resep['no_resep']) ? $resep['no_resep'] : $resep['no_reseptur'];
                $list[] = [
                    'id' => $nomor,
                    'text' => $nomor
                ];
            }
            
            return DocoHelpers::response($list);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}