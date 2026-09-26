<?php

/**
 * @author : iqbal (iqbal@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\worklist;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PrintEtiketNewAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data = $request->get();
        $isOral = ($data['is_oral'] == 'true') ? 1 : 0;

        $path = Yii::getAlias("@download") . "/cetak-etiket-oral.pdf";
        try {
            $response = Yii::$app->docoRest->apotek->get('worklist/print-etiket-new',[
                'save_to' => $path,
                'query' => [
                        'identifier' => $data['identifier'],
                        'is_oral' => $isOral,
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