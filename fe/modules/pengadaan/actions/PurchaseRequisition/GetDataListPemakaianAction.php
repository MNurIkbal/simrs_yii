<?php

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataListPemakaianAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = []; 
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan,[
            'url' => 'purchase-requisition/get-data-list-pemakaian',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'id' => $request->get('id'),
                    'data_pemakaian' => $request->get('data_pemakaian')
                ]
            ],
        ]);
        $body = ArrayHelper::getValue($response, 'data', []);
        $meta = ArrayHelper::getValue($response, '_meta', []);

        $no = $request->get('start',1);
        foreach ($body as $key => $value) {
            $no++;
            $value['tanggal_transaksi'] = isset($value['tanggal_transaksi']) ? date('d-m-Y', strtotime($value['tanggal_transaksi'])) : null;
            $value['ruangan_nama'] = ArrayHelper::getValue($value, 'ruangan_nama');
            $value['jumlah_pemakaian'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'qtystok_out'));
            $value['jenis_pemakaian'] = ArrayHelper::getValue($value, 'keterangan');

            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($meta, 'totalCount');
        $result['recordsFiltered'] = ArrayHelper::getValue($meta, 'totalCount');
        return $result;
    }
}
