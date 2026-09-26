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
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
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
        }

        $url = 'laporan-summary-stok-opname/export-excel?'. http_build_query($filter);
        $path = Yii::getAlias("@download") . "/laporan_summary_stok_opname.xlsx";
        try {
            $response = Yii::$app->docoRest->apotek->get($url, [
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
