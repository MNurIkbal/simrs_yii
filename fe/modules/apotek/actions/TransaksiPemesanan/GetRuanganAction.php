<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiPemesanan;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class GetRuanganAction extends Action {
    protected $_instalasi_gdf = DocoConstants::INSTALASI_GUDANG_FARMASI;

    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = Yii::$app->docoRest->apotek->get('allow/get-ruangan?instalasi_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value) {
                if ($ruangan_id != $value['ruangan_id']) {
                    $result['output'][] = [
                        'id' => $value['ruangan_id'],
                        'name' => $value['ruangan_nama']
                    ];
                }
            }

            if (count($result['output']) == 1) {
                $result['selected'] = $result['output'][0]["id"];
            }

            if ($parent_label == $this->_instalasi_gdf) {
                $result['selected'] = '25';
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}