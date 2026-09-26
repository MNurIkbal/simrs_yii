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
use GuzzleHttp\Exception\RequestException;

class PrintResepAction extends Action {
    public function run($id, $noresep, $nomor) {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resep-".$noresep.".pdf";
        try {
            if(Yii::$app->report->enabled){
                $urlReport = 'print-resep-report';
                $post = ['id' => @$id, 'noresep' => @$noresep, 'nomor' => @DocoHelpers::decrypt($nomor)];

                Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $post,
                    'manualRender'=>function() use($post,$path){
                        $response = $this->_restPendaftaran->post('inf-reseptur/print-resep',[
                            'form_params' => $post,
                            'save_to' => $path
                        ]);
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = Yii::$app->docoRest->apotek->get('inf-reseptur/print-resep',[
                'save_to' => $path,
                'query' => [
                        'id' => @$id,
                        'noresep' => @$noresep,
                        'nomor' => @DocoHelpers::decrypt($nomor),
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