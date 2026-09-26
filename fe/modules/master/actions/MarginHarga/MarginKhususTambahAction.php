<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\MarginKhususForm;
use app\modules\master\models\MarginKhususDetailForm;

class MarginKhususTambahAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', 'Margin Khusus Rumah Sakit');
        $mMarginKhusus = new MarginKhususForm;
        $mMarginKhususDetail = new MarginKhususDetailForm;
        $formName = substr(strrchr(get_class($mMarginKhusus), "\\"), 1);
        $formNameDetail = substr(strrchr(get_class($mMarginKhususDetail), "\\"), 1);
        $id = null;

        try {
            if ($request->post()) {
                $mMarginKhusus->attributes = $request->post('MarginKhususForm');
                $mMarginKhusus->additional_data = null;
                if(!$mMarginKhusus->validate()) {
                    $errors = DocoHelpers::parseError($mMarginKhusus->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }

                $margin_detail = $request->post('MarginKhususDetailForm',[]);
                $errors_margin_detail = [];
                $temp_value_margin_detail = [];
                foreach ($margin_detail as $key_detail => $value_detail) {
                    $mMarginKhususDetail->attributes = $value_detail;
                    if(!$mMarginKhususDetail->validate()){
                        $errors = DocoHelpers::parseError($mMarginKhususDetail->errors, $formNameDetail.'['.$key_detail.']');
                        $errors_margin_detail = array_merge($errors,$errors_margin_detail);
                    }

                    if(!in_array($value_detail['jenisobat_id'], $temp_value_margin_detail)){
                        $temp_value_margin_detail[] = $value_detail['jenisobat_id'];
                    } else {
                        return DocoHelpers::responseTemplate(
                            422,
                            'Error',
                            DocoHelpers::parseError([
                                'jenisobat_id' => ['Jenis Obat Tidak Boleh Sama']
                            ], $formNameDetail.'['.$key_detail.']')
                        );
                    }
                }

                if(is_array($errors_margin_detail) && count($errors_margin_detail)>=1){
                    return DocoHelpers::responseTemplate(422, 'Error', $errors_margin_detail);
                }

                $response = Yii::$app->docoRest->master->post('margin-harga/create-margin-khusus', [
                    'form_params' => [
                        'header' => $mMarginKhusus->attributes,
                        'detail' => json_encode($request->post('MarginKhususDetailForm'))
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);
            } else {
                $jenisobat_request = Yii::$app->docoRest->master->get('jenis-obat-alkes/get-all-data');
                $body = json_decode($jenisobat_request->getBody(), true);
                $response_jenis_obat = $body['response']['data'];
                $jenis_obat = ArrayHelper::map($response_jenis_obat,'jenisobatalkes_id','jenisobatalkes_nama');
                return $this->controller->renderAjax('form_margin_khusus', get_defined_vars());
            }
        } catch (RequestException $e) {
           return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}