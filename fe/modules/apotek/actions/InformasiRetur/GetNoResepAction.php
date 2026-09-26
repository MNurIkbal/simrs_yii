<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetNoResepAction extends Action {
    public function run() {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->controller->_restApotek->request('POST', 'inf-retur/get-no-resep', [
                            'form_params' => [
                                'term' => $_GET['q']['term']
                            ]
                        ]);

            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['noresep'],
                    'text' => $value['noresep']
                ];
            }

            $total = count($body['response']);
            $return = [
                'result' => $data,
                'total_count' => $total,
                'incomplete_results' => false
            ];

            return DocoHelpers::response($return);
        }
    }
}