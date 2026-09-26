<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanLeadTimeResep;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
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

        try {
            $response = $this->controller->_restApotek->get('laporan-lead-time-resep/get-data?' . http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_resep'] = date("d-M-Y", strtotime($value['tgl_resep']));
                $value['jam_resep_masuk'] = is_null($value['jam_resep_masuk']) ? null : date("d-M-Y H:i:s", strtotime($value['jam_resep_masuk']));
                $value['jam_resep_dibayar'] = is_null($value['jam_resep_dibayar']) ? null : date("d-M-Y H:i:s", strtotime($value['jam_resep_dibayar']));
                $value['jam_production'] = is_null($value['jam_production']) ? null : date("d-M-Y H:i:s", strtotime($value['jam_production']));
                $value['jam_diserahkan'] = is_null($value['jam_diserahkan']) ? null : date("d-M-Y H:i:s", strtotime($value['jam_diserahkan']));
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
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
