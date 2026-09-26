<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    protected $_status_resep = [
        "Belum Proses",
        "Dalam Proses",
        "Diserahkan",
        "Batal Reseptur",
    ];

    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        if (isset($yiiRestfulParams['advanced-filter']['status_reseptur'])) {
            $status_index = $yiiRestfulParams['advanced-filter']['status_reseptur'];
            $yiiRestfulParams['advanced-filter']['status_reseptur'] = $this->_status_resep[$status_index];
        }

        try {
            $daterange = isset($yiiRestfulParams['advanced-filter']['tgl_resep_dibuat']) ? $yiiRestfulParams['advanced-filter']['tgl_resep_dibuat'] : date('d-M-Y');
            $filename = "/informasi_reseptur_".$daterange.".xlsx";
            $path = Yii::getAlias("@download") . $filename;
            $response = Yii::$app->docoRest->apotek->get('inf-reseptur/export-excel',[
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