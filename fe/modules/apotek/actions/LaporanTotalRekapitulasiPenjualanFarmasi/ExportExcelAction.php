<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanTotalRekapitulasiPenjualanFarmasi;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if(isset($yiiRestfulParams['advanced-filter']['tgl'])) {
            $yiiRestfulParams['advanced-filter']['tgl_pelayanan'] = $yiiRestfulParams['advanced-filter']['tgl'];

            unset($yiiRestfulParams['advanced-filter']['tgl']);
        }

        if(isset($yiiRestfulParams['advanced-filter']['jenisobatalkes_nama'])) {
            $yiiRestfulParams['advanced-filter']['jenisobatalkes_id'] = $yiiRestfulParams['advanced-filter']['jenisobatalkes_nama'];

            unset($yiiRestfulParams['advanced-filter']['jenisobatalkes_nama']);
        }

        $url = 'lap-total-rekapitulasi-penjualan-farmasi/export-excel?'. http_build_query($yiiRestfulParams);
        $doc_name = $this->setDocName($yiiRestfulParams['advanced-filter']);
        $path = Yii::getAlias("@download") . $doc_name;
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

    public function setDocName($params) {
        if(isset($params['tgl_pelayanan'])) {
            $exp = explode(' - ', $params['tgl_pelayanan']);
            $tgl_awal = $exp[0];
            $tgl_akhir = $exp[1];
            $tgl_pelayanan = "_".date('dmY', strtotime($tgl_awal))." - ".date('dmY', strtotime($tgl_akhir));
        } else {
            $tgl_pelayanan = "_".date('dMY');
        }

        return "/lap_total_rekap_penjualan_farmasi".$tgl_pelayanan.".xlsx";
    }
}
