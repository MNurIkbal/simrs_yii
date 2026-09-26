<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPurchaseOrderBarang;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($filter['advanced-filter']['tgl_po'])) {
            $tgl_po = explode(' - ', $filter['advanced-filter']['tgl_po']);
            $tgl_awal = $tgl_po[0];
            $tgl_akhir = $tgl_po[1];
            $filter['advanced-filter']['tgl_po_awal'] = $tgl_awal;
            $filter['advanced-filter']['tgl_po_akhir'] = $tgl_akhir;
        }

        $url = 'lap-rekap-purchase-order-barang/export-excel';
        $path = Yii::getAlias("@download") . "/laporan_rekap_purchase_order_barang.xlsx";
        try {
            $response = Yii::$app->docoRest->pengadaan->get($url, [
                'query' => $filter,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
