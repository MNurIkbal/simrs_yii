<?php

namespace Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;

class DownloadTemplateAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pengajuanKlaimId = $request->get('pengajuanklaim_id');
        if (!$pengajuanKlaimId) return DocoHelpers::response(['message' => 'pengajuanklaim_id must be set !'], 400);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/template-alokasi-pembayaran.xlsx";
            $response = $this->_restPenjamin->get("transaksi-alokasi-pembayaran/download-template?pengajuanklaim_id=$pengajuanKlaimId", [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}
