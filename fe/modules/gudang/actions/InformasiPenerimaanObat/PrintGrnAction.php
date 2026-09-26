<?php

/**
 * @author : iqbal (iqbal@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPenerimaanObat;

use app\components\DocoConstants;
use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PrintGrnAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data = $request->get();
        $path = Yii::getAlias("@download") . "/print-grn.pdf";
        try {
            if(Yii::$app->report->enabled){
                $id = $request->get('id', null);
                $query = [
                    'id' => @DocoHelpers::decrypt($data['id']),
                    'type' => $data['type']
                ];
    
                $urlReport = 'grn';
                if ($data['type'] == DocoConstants::JENIS_BARANG){
                    $urlReport = 'grn-barang';
                }
                $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;
    
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        Yii::$app->docoRest->gudang->get('informasi-penerimaan/print-grn',[
                            'query' => $query,
                            'save_to' => $path
                        ]);
    
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = Yii::$app->docoRest->gudang->get('informasi-penerimaan/print-grn',[
                'save_to' => $path,
                'query' => [
                    'id' => @DocoHelpers::decrypt($data['id']),
                    'type' => $data['type']
                ],
            ]);
            $result = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}