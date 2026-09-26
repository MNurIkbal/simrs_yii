<?php


namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PrintMultipleResep extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $obatalkes_nama = $request->get('obatalkes_nama', []);
        $noresep = $request->get('noresep', []);
        $add_comment = $request->get('add_comment');
        $path = Yii::getAlias("@download") . "/cetak-multiple-obat-".$noresep[0].".pdf";
        try {
            $response = Yii::$app->docoRest->apotek->get('inf-reseptur/print-multiple-resep',[
                'save_to' => $path,
                'query' => [
                        'obatalkes_nama' => $obatalkes_nama,
                        'noresep' => $noresep,
                        'add_comment' => $add_comment,
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