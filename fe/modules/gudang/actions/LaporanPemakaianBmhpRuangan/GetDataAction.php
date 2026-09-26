<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanPemakaianBmhpRuangan;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\helpers\HandlingValueHelper;
use yii\helpers\ArrayHelper;

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

        try {
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->gudang,[
                'url' => 'lap-pemakaian-bmhp-ruangan/index',
                'method' => 'GET',
                'payload' => [
                    'query' => $yiiRestfulParams
                ],
            ]);
            $body = ArrayHelper::getValue($response, 'data', []);
            $meta = ArrayHelper::getValue($response, '_meta', []);
            
            $no = $request->get('start',1);
            foreach ($body as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_transaksi'] = date('d M Y H:i:s', strtotime($value['tgl_transaksi']));
                $value['qty_input'] = DocoHelpers::formatNumber($value['qty_input']);
                $value['harga_netto_konversi'] = DocoHelpers::formatNumber($value['harga_netto_konversi']);
                $value['total_harga'] = DocoHelpers::formatNumber($value['total_harga']);
                $value['catatan'] = HandlingValueHelper::nullValue($value['catatan']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($meta, 'totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($meta, 'totalCount');
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
