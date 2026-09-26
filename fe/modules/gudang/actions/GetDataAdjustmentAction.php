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
use app\components\helpers\HandlingValueHelper;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataAdjustmentAction extends Action {
    public function run() {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $get = $request->get();
        $yiiRestfulParams['advanced-filter']['no_adjusmen'] = $get['no_adjusmen'];
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        unset($yiiRestfulParams['page']);
        unset($yiiRestfulParams['per-page']);

        try {
            $response = Yii::$app->docoRest->gudang->get('informasi-adjustment-obat-alkes/data-adjustment?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $data_obat = $body['response']['data'];
            $no = $request->get('start', 1);
            foreach ($data_obat as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['harga_netto'] = DocoHelpers::formatNumber($value['harga_netto']);
                $value['no_batch'] = HandlingValueHelper::nullValue($value['no_batch']);
                $value['keterangan'] = HandlingValueHelper::nullValue($value['keterangan']);
                $value['alasan'] = HandlingValueHelper::nullValue($value['alasan']);
                $value['tgl_kadaluarsa'] = !empty($value['tgl_kadaluarsa']) ? date("d M Y", strtotime($value['tgl_kadaluarsa'])) : '-';
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