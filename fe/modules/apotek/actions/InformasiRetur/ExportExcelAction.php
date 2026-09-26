<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

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
        $yiiRestfulParams = $this->controller->getFilter($request);

        try {
            $tgl_retur = $yiiRestfulParams['advanced-filter']['tgl_retur'];
            $daterange = isset($tgl_retur) ? $tgl_retur : date('d-M-Y');
            $filename = "/Informasi_retur_Obat_BMHP-".$daterange.".xlsx";
            $path = Yii::getAlias("@download") . $filename;
            $response = Yii::$app->docoRest->apotek->get('inf-retur/export-excel', [
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}