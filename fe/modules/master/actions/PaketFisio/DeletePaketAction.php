<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class DeletePaketAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $daftarPaketFisioId = $request->get('id');
        $daftarPaketFisioId = DocoHelpers::decrypt($daftarPaketFisioId);
        try {
            $response = (new DocoHelpers)->guzzleExec($this->restMaster, [
                'method' => 'POST',
                'url' => 'paket-fisio/delete-data',
                'payload' => [
                    'form_params' => [
                        'daftarpaketfisio_id' => $daftarPaketFisioId
                    ]
                ],
                'with_metadata' => true
            ]);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            (new DocoHelpers)->logError($e);
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
