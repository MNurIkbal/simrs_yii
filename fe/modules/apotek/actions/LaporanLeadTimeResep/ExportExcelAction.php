<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanLeadTimeResep;

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
        $url = 'laporan-lead-time-resep/export-excel?'. http_build_query($yiiRestfulParams);
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
        if(isset($params['tgl_resep'])) {
            $exp = explode(' - ', $params['tgl_resep']);
            $tgl_awal = $exp[0];
            $tgl_akhir = $exp[1];
            $tgl_resep = "_".date('dmY', strtotime($tgl_awal))." - ".date('dmY', strtotime($tgl_akhir));
        } else {
            $tgl_resep = "_".date('dMY');
        }

        if(isset($params['ruangan_id'])) {
            $ruangan = "_".$params['ruangan_id'];
        } else {
            $ruangan = "";
        }

        return "/laporan-lead-time-resep".$tgl_resep.$ruangan.".xlsx";
    }
}
