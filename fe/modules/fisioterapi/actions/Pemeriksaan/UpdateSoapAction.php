<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\fisioterapi\models\SoapForm;
use yii\validators\Validator;

class UpdateSoapAction extends BaseCurrentAction
{
    public function run()
    {
        $this->_rest = Yii::$app->docoRest->fisioterapi;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $validator = new Validator();
        $request = Yii::$app->request;
        $soapFisioterapiIdEnc = $request->get('soapfisioterapi_id');
        // $instalasiId = $request->post('instalasi_id');
        if ($validator->isEmpty($soapFisioterapiIdEnc)) {
            return DocoHelpers::responseTemplate(422, 'Error', 'soapfisioterapi_id cannot be blank !');
        }
        // if ($validator->isEmpty($instalasiId)) {
        //     return DocoHelpers::responseTemplate(422, 'Error', 'instalasi_id cannot be blank !');
        // }
        $soapFisioterapiId = DocoHelpers::decrypt($soapFisioterapiIdEnc);
        $model = new SoapForm;
        $formName = 'SoapForm';
        $model->attributes = $request->post('SoapForm', []);
        $model->soapfisioterapi_id = $soapFisioterapiId;
        if (!$model->validate()) {
            $response = $model->errors;
            $errors = DocoHelpers::parseError($response, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }

        $jsonForm = [
            'soapfisioterapi_id' => $soapFisioterapiId,
            'terapis_id' => $model->terapis,
            'subject' => DocoHelpers::purifyText($model->subject),
            'object' => DocoHelpers::purifyText($model->object),
            'planning' => DocoHelpers::purifyText($model->planning),
            'catatan_dokter' => DocoHelpers::purifyText($model->catatan_dokter),
            'instruksi' => DocoHelpers::purifyText($model->instruksi),
            'a_diag_utama' => DocoHelpers::purifyText($model->a_diag_utama),
            'a_diag_penyerta' => DocoHelpers::purifyText($model->a_diag_penyerta),
            'diagnosa_fungsi' => DocoHelpers::purifyText($model->diagnosa_fungsi),
            'prosedur_kerja' => DocoHelpers::purifyText($model->prosedur),
            'goal' => DocoHelpers::purifyText($model->goal),
            // 'instalasi_id' => DocoHelpers::decrypt($instalasiId)
        ];

        $response = (new DocoHelpers)->guzzleExec($this->_rest, [
            'method' => 'POST',
            'url' => 'soap/update-soap',
            'payload' => [
                'form_params' => $jsonForm
            ]
        ]);

        // $response = Yii::$app->docoRest->fisioterapi->post('soap/update-soap', ['json' => $jsonForm]);
        // $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }
}
