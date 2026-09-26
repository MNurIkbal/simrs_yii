<?php

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\fisioterapi\models\SoapRanapForm;
use yii\helpers\ArrayHelper;

class StoreSoapAction extends BaseCurrentAction
{
    private function hitApi($payload)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'POST',
            'url' => "soap-ranap/save-soap",
            'payload' => [
                'form_params' => $payload
            ],
            'with_metadata' => true
        ]);
        return DocoHelpers::response($response);
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $getParam = Yii::$app->request->get();
        $pendaftaranIdEnc = ArrayHelper::getValue($getParam, 'pendaftaran_id');
        $programTerapiIdsStringEnc = ArrayHelper::getValue($getParam, 'program_terapi_ids');
        if (!$programTerapiIdsStringEnc) {
            return DocoHelpers::responseTemplate(422, 'Error', [], [
                'title' => 'Peringatan!',
                'text' => 'Program terapi ids tidak boleh kosong',
                'message' => 'Program terapi ids tidak boleh kosong',
            ]);
        }
        $pendaftaranId = DocoHelpers::decrypt($pendaftaranIdEnc);
        $programTerapiIdsEnc = explode(',', $programTerapiIdsStringEnc);
        $programTerapiIds = [];
        foreach ($programTerapiIdsEnc as $key => $value) {
            $programTerapiIds[] = DocoHelpers::decrypt($value);
        }
        $model = new SoapRanapForm;
        $formName = 'SoapRanapForm';
        $model->attributes = Yii::$app->request->post('SoapRanapForm', []);
        $hasDiagnosa = !empty($model->a_diag_utama) && !empty($model->a_diag_penyerta);
        if ($hasDiagnosa) {
            if (in_array($model->a_diag_utama, $model->a_diag_penyerta)) {
                return DocoHelpers::responseTemplate(422, 'Error', [], [
                    'title' => 'Peringatan!',
                    'text' => 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.',
                    'message' => 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.',
                ]);
            }
        }
        if (!$model->validate()) {
            $response = $model->errors;
            $errors = DocoHelpers::parseError($response, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
        $jsonForm = [
            'tgl_soap' => $model->tgl_soap,
            'terapis_id' => $model->terapis,
            'subject' => DocoHelpers::purifyText($model->subject),
            'object' => DocoHelpers::purifyText($model->object),
            'planning' => DocoHelpers::purifyText($model->planning),
            'a_diag_utama' => DocoHelpers::purifyText($model->a_diag_utama),
            'a_diag_penyerta' => DocoHelpers::purifyText($model->a_diag_penyerta),
            'catatan_dokter' => DocoHelpers::purifyText($model->catatan_dokter),
            'instruksi' => DocoHelpers::purifyText($model->instruksi),
            'pendaftaran_id' => $pendaftaranId,
            'program_terapi_ids' => $programTerapiIds,
            'status_program_id' => DocoConstants::STATUS_PROGRAM_FISIO_CLOSE,
            'diagnosa_fungsi' => DocoHelpers::purifyText($model->diagnosa_fungsi),
            'prosedur_kerja' => DocoHelpers::purifyText($model->prosedur),
            'goal' => DocoHelpers::purifyText($model->goal),
        ];
        return $this->hitApi($jsonForm);
    }
}
