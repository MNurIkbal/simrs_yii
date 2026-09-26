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
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataObatAction extends Action {
    public function run($id) {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try{
            $response = $this->controller->_restApotek->get('inf-retur/get-detail', [
                'query' => [
                    'id' => $id
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $total = 0;
            foreach ($body['response'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $total += $value['total'];
                $value['hargasatuan'] = "Rp. ".number_format($value['hargasatuan'], 0, ',','.');
                $value['total'] = "Rp. ".number_format($value['total'], 0, ',','.');
                $data[$key] = $value;
            }

            $result['totalobat'] = "Rp. ".number_format($total, 0,',','.');
            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = count($body['response']);
            $result['recordsFiltered'] = count($body['response']);

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