<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PilihRanapAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $program_id = $request->get('program_id');
        $form_params = [
            'pendaftaran_id' => DocoHelpers::decrypt($pendaftaran_id),
            'programterapi_id' => DocoHelpers::decrypt($program_id)
        ];
        try {
            $response = Yii::$app->docoRest->fisioterapi->post('informasi-pasien-fisioterapi/pilih-ranap', [
                'form_params' => $form_params
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($form_params);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            return DocoHelpers::response($response);
        }
    }
}
