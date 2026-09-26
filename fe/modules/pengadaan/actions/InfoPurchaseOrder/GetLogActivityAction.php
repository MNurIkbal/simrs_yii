<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetLogActivityAction extends Action {
    public function run($id,$type_po) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['transaksi_id'] = $id;
        $filter['advanced-filter']['tipe'] = DocoHelpers::decrypt($type_po) == 'obat' ? 'PO' : 'PONONMEDIS';

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->pengadaan->get('info-purchase-order/get-log-activity', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $log_data = $body['response']['data'];
            $no = $request->get('start', 1);
            foreach ($log_data as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['transaksi_id']);
                unset($value['transaksi_id']);
                $value['primary'] = $primaryKey;
                $value['tgl'] = date('d M Y H:i:s', strtotime($value['tgl']));
                $value['alasan'] = !empty($value['alasan']) ? $value['alasan'] : '-';
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