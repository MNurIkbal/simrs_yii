<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\helpers\HandlingValueHelper as SetValue;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($filter['advanced-filter']['tglformulir'])) {
            $tglformulir = explode(' - ', $filter['advanced-filter']['tglformulir']);
            $tgl_awal = $tglformulir[0];
            $tgl_akhir = $tglformulir[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tglformulir_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tglformulir_akhir'] = $tgl_akhir_format;
            unset($filter['advanced-filter']['tglformulir']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $url = 'laporan-summary-stok-opname/get-data?' . http_build_query($filter);
            $response = Yii::$app->docoRest->apotek->get($url);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tglformulir'] = !isset($value['tglformulir']) ? "-" : DocoHelpers::convDateTime($value['tglformulir'], true);
                $value['tgl_validasi'] = !isset($value['tgl_validasi']) ? "-" : DocoHelpers::convDateTime($value['tgl_validasi'], true);
                $value['konversi'] = SetValue::nullValue(DocoHelpers::formatNumber($value['konversi']));
                $value['weighted_average'] = SetValue::nullValue(DocoHelpers::formatNumber($value['weighted_average']));
                $value['system_stock_qty'] = SetValue::nullValue(DocoHelpers::formatNumber($value['system_stock_qty']));
                $value['physical_stock_qty'] = SetValue::nullValue(DocoHelpers::formatNumber($value['physical_stock_qty']));
                $value['variance_qty'] = SetValue::nullValue(DocoHelpers::formatNumber($value['variance_qty']));
                $value['opening_total_batch_cost'] = SetValue::nullValue(DocoHelpers::formatNumber($value['opening_total_batch_cost']));
                $value['ending_total_batch_cost'] = SetValue::nullValue(DocoHelpers::formatNumber($value['ending_total_batch_cost']));
                $value['selisih_batch_cost'] = SetValue::nullValue(DocoHelpers::formatNumber($value['selisih_batch_cost']));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['message'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }
}
