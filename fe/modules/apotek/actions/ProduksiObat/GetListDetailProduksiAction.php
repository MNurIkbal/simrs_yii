<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetListDetailProduksiAction extends Action {
    public function run() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-produksi-obat/get-list-produksi',
                'payload' => [
                    'form_params' => [
                        'id' => DocoHelpers::decrypt($request->get('id'))
                    ]
                ],
            ]);
            
            $no = $request->get('start',1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['produksiobatalkesdetail_id']);
                $value['obatalkes_nama'] = is_null($value['obatalkes_nama']) ? "-" : $value['obatalkes_nama'];
                $value['qty_produksi'] = is_null($value['qty_produksi']) ? "-" : $value['qty_produksi'];
                $value['satuan'] = is_null($value['satuan']) ? "-" : $value['satuan'];
                $value['pegawai_pemesanan'] = is_null($value['pegawai_pemesanan']) ? "-" : $value['pegawai_pemesanan'];
                $value['tgl_pesanan'] = is_null($value['tglpemesanan']) ? "-" : date("j M Y H:i:s", strtotime($value['tglpemesanan']));
                $value['status_produksi'] = is_null($value['status_produksi']) ? "-" : $value['status_produksi'];
                $value['harga_netto'] = is_null($value['harganetto_baru']) ? "-" : DocoHelpers::formatNumber($value['harganetto_baru']);
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                        'class' => 'btn btn-sm btn-success',
                        'data-source'=>"/apotek/inf-produksi-obat/expand?id=".$primaryKey,
                        'onclick'=> 'docoHelper.detail(this)'
                ]);

                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
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
