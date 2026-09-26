<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class CekTransaksiPaketAction extends BaseCurrentAction
{
    public function run()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($request->get('id'));
            // $restMaster = Yii::$app->docoRest->master->get('tipe-paket/cek-transaksi-paket', [
            //     'query' => ['id' => $id]
            // ]);
            // $body = json_decode($restMaster->getBody(), true);
            $body = null;
            $count = 2;
            return DocoHelpers::response($count);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
