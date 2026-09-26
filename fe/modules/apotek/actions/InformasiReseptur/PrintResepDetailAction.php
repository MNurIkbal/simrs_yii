<?php

/**
 * @author : iqbal (iqbal@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PrintResepDetailAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data = $request->get();
        $path = Yii::getAlias("@download") . "/cetak-resep-detail.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');

        $query = [
            'nomor' => @DocoHelpers::decrypt($data['nomor']),
            'id' => @DocoHelpers::decrypt($data['id']),
            'type' => !empty($data['type']) ? $data['type'] : 'resep',
            'nama_pegawai' => $userIdentity['nama_pegawai'],
        ];

        try {
            if(Yii::$app->report->enabled){
                $urlReport = 'resep-dokter';
                $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;
    
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        $response = Yii::$app->docoRest->apotek->get('inf-reseptur/print-resep-detail',[
                            'query' => $query,
                            'save_to' => $path
                        ]);
    
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }

            $response = Yii::$app->docoRest->apotek->get('inf-reseptur/print-resep-detail',[
                'save_to' => $path,
                'query' => $query
            ]);
            $result = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            Yii::warning($e->getMessage());
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
