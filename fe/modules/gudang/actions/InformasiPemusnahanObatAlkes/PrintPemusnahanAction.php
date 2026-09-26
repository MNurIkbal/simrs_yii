<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPemusnahanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class PrintPemusnahanAction extends Action {
    public function run($id, $nopemusnahan) {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemusnahan-".$nopemusnahan.".pdf";
        try {
            $response = Yii::$app->docoRest->gudang->get('inf-pemusnahan-obat/print-pemusnahan',[
                'save_to' => $path,
                'query' => [
                    'id' => $id,
                    'nopemusnahan' => $nopemusnahan
                ],
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}