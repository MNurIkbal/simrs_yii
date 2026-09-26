<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanRekapitulasiPenjualan;

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
        $url = 'lap-rekapitulasi-penjualan/export-excel?'. http_build_query($yiiRestfulParams);
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

        if(isset($params['kode_obat'])) {
            $kode_obat = "_".$params['kode_obat'];
        } else {
            $kode_obat = "";
        }

        if(isset($params['nama_obat'])) {
            $nama_obat = "_".$params['nama_obat'];
        } else {
            $nama_obat = "";
        }
        return "/lap_rekap_penjualan_farmasi".$tgl_pelayanan.$kode_obat.$nama_obat.".xlsx";
    }
}