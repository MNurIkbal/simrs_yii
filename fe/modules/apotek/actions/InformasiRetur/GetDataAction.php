<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {

    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->controller->getFilter($request);

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->controller->_restApotek->get('inf-retur/index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                $value['id_retur'] = $value['returresep_id'];
                $value['returresep_id'] = DocoHelpers::encrypt($value['returresep_id']);
                $value['rowNum'] = $no;
                $value['tgl_retur'] = date('d M Y', strtotime($value['tgl_retur']));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        }catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}