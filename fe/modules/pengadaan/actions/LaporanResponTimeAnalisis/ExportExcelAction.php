<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanResponTimeAnalisis;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    public function run($tipe) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', null);
        $titleType = $tipe == 'obat' ? 'medis' : 'nonmedis';
        $url = 'lap-respon-time-analisis/export-excel';
        $path = Yii::getAlias("@download") . "/laporan_respon_time_analisis_".$titleType.".xlsx";
        try {
            $response = Yii::$app->docoRest->pengadaan->get($url, [
                'save_to' => $path,
                'query' => [
                    'tipe' => $tipe,
                    'start_date' => $request->get('start_date', null),
                    'end_date' => $request->get('end_date', null)
                ]
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
