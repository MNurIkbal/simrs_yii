<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

class HistoryFisioterapiAction extends BaseCurrentAction
{
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $pasien_id = DocoHelpers::decrypt($request->get('pasien_id'));

            /**
             * Fetch API.
             */
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/get-history-fisioterapi', [
                'query' => [
                    'pasien_id' => $pasien_id
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $resultData = ArrayHelper::getValue($body, 'response.data');
            return $this->controller->renderAjax('modal_history_fisioterapi', compact('resultData', 'pasien_id'));
        } catch (\Exception $th) {
            return DocoHelpers::responseTemplate(500, $th->getMessage());
        }
    }
}
