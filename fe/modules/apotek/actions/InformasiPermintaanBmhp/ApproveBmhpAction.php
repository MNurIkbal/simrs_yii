<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ApproveBmhpAction extends Action {
    public function run($id) {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $response = Yii::$app->docoRest->apotek->post('informasi-permintaan-bmhp/approve-bmhp', [
                'query' => [
                    'pendaftaran_id' => DocoHelpers::decrypt($id),
                    'ruangan_id' => $ruangan_id
                ],
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $error = json_decode($e->getResponse()->getBody(), true);
            return DocoHelpers::response($error);
        } catch (\Exception $e) {
            return DocoHelpers::response(['text' => $e->getMessage()],422);
        }
    }
}