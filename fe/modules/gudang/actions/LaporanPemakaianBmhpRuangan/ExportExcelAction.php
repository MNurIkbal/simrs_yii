<?php

/**
 * @author : Muhamad Lukman Hakim (hakim.muhamad@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanPemakaianBmhpRuangan;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\helpers\HandlingValueHelper;

class ExportExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $path = Yii::getAlias("@download") . "/laporan-pemakaian-bmhp-ruangan.xlsx";
            $response = Yii::$app->docoRest->gudang->get('lap-pemakaian-bmhp-ruangan/export-excel', [
                'save_to' => $path,
                'query' => $yiiRestfulParams
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
