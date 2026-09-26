<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class GetDataLogAction extends Action {
    public function run() {
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
        $yiiRestfulParams['id'] = $request->get()['id'];
        $yiiRestfulParams['type'] = $request->get()['type'];

        try {
            $response = Yii::$app->docoRest->apotek->get(
                'log-perubahan-resep/get-data?'.http_build_query($yiiRestfulParams), 
                ['form_params' => []]
            );
            $body = json_decode($response->getBody(), True);

            $data_obat = $body['response']['data'];
            $no = $request->get('start',1);
            $data = [];

            foreach ($data_obat as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tanggal_perubahan'] = date('d M Y H:i:s', strtotime($value['tanggal_perubahan']));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = 'x-'.$e->getMessage();
            return $result;
        }
    }
}
