<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\fisioterapi\models\SoapForm;

class StoreSoapAction extends BaseCurrentAction
{
    public function run()
    {
        $pendaftaranIdEnc = Yii::$app->request->get('pendaftaran_id');
        $pendaftaranId = DocoHelpers::decrypt($pendaftaranIdEnc);
        $loginPemakaiId = Yii::$app->docoVars->user('loginpemakai_id');
        
        // Create array, casting & decrypt programterapi_id
        $programTerapiIdEnc = Yii::$app->request->get('program_terapi_id');
        $programTerapiIdsArr = explode(',', $programTerapiIdEnc);
        $programTerapiIds = [];
        foreach ($programTerapiIdsArr as $value) {
            $programTerapiIds[] = (int) DocoHelpers::decrypt($value);
        }

        // Create array, casting & decrypt programterapidetail_id
        $programTerapiDetailIdEnc = Yii::$app->request->get('program_terapi_detail_id');
        $programTerapiDetailIdsArr = explode(',', $programTerapiDetailIdEnc);
        $programTerapiDetailIds = [];
        foreach ($programTerapiDetailIdsArr as $value) {
            $programTerapiDetailIds[] = (int) DocoHelpers::decrypt($value);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SoapForm;
        $formName = 'SoapForm';
        $model->attributes = Yii::$app->request->post('SoapForm', []);
        $hasDiagnosa = !empty($model->a_diag_utama) && !empty($model->a_diag_penyerta);
        
        if ($hasDiagnosa && in_array($model->a_diag_utama, $model->a_diag_penyerta)) {
            return DocoHelpers::responseTemplate(422, 'Error', [], [
                'title' => 'Peringatan!',
                'text' => 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.',
                'message' => 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.',
            ]);
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
            'program_terapi_id' => $programTerapiIds,
            'program_terapi_detail_id' => $programTerapiDetailIds,
            'loginpemakai_id' => $loginPemakaiId,
            'status_program_id' => DocoConstants::STATUS_PROGRAM_FISIO_CLOSE,
            'diagnosa_fungsi' => DocoHelpers::purifyText($model->diagnosa_fungsi),
            'prosedur_kerja' => DocoHelpers::purifyText($model->prosedur),
            'goal' => DocoHelpers::purifyText($model->goal),
        ];
        $response = Yii::$app->docoRest->fisioterapi->post('soap/save-soap', ['json' => $jsonForm]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::response($body);
    }
}
