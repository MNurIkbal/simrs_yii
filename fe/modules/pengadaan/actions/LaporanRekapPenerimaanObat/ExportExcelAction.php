<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPenerimaanObat;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($filter['advanced-filter']['tgl_penerimaan'])) {
            $tgl_po = explode(' - ', $filter['advanced-filter']['tgl_penerimaan']);
            $tgl_awal = $tgl_po[0];
            $tgl_akhir = $tgl_po[1];
            $filter['advanced-filter']['tgl_penerimaan_awal'] = $tgl_awal;
            $filter['advanced-filter']['tgl_penerimaan_akhir'] = $tgl_akhir;
        }

        $url = 'lap-rekap-penerimaan-obat/export-excel';
        $path = Yii::getAlias("@download") . "/laporan_rekap_penerimaan_obat.xlsx";
        
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
