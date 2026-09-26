<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\fisioterapi\models\SoapForm;
use app\modules\fisioterapi\models\SoapRanapForm;
class UpdateSoapAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $soapFisioterapiIdEnc = Yii::$app->request->get('soapfisioterapi_id');
        if(!$soapFisioterapiIdEnc) {
            return DocoHelpers::responseTemplate(422, 'Error', 'soapfisioterapi_id cannot be blank !');
        }
        $soapFisioterapiId = DocoHelpers::decrypt($soapFisioterapiIdEnc);
        $model = new SoapRanapForm;
        $formName = 'SoapRanapForm';
        $model->attributes = Yii::$app->request->post('SoapRanapForm', []);
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
        ];
        $response = Yii::$app->docoRest->fisioterapi->post('soap-ranap/update-soap', ['json' => $jsonForm]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }
}
