<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\fisioterapi\models\OrderPenunjangForm;

class UpdateDataAction extends BaseCurrentAction
{
    private function convertCatatan($catatan)
    {
        $explode = explode(': ', $catatan);
        $result = ArrayHelper::getValue($explode, 1);
        if(!$result) {
            $result = ArrayHelper::getValue($explode, 0);
        }
        return $result;
    }

    private function getChildsFromPayload()
    {
        $request = Yii::$app->request;
        $datas = $request->post('periksalab', '{}');
        $datas = json_decode($datas, true);
        $childs = [];
        foreach ($datas as $key => $value) {
            $catatan = ArrayHelper::getValue($value, 'catatan');
            $catatan = $this->convertCatatan($catatan);
            $childs[$key]['daftartindakan_id'] = ArrayHelper::getValue($value, 'daftartindakan_id');
            $childs[$key]['tariftindakan_id'] = ArrayHelper::getValue($value, 'tariftindakan_id');
            $childs[$key]['parentdaftartindakan_id'] = ArrayHelper::getValue($value, 'parentdaftartindakan_id');
            $childs[$key]['catatan'] = $catatan;
            $childs[$key]['is_cyto'] = null;
            $childs[$key]['is_paketfisio'] = ArrayHelper::getValue($value, 'is_paketfisio');
            $childs[$key]['golongan_id'] = null;
            $childs[$key]['kegiatan_id'] = null;
            $childs[$key]['programterapi_deleted'] = ArrayHelper::getValue($value, 'programterapi_deleted', false);
        }
        return $childs;
    }

    private function getParentFromPayload()
    {
        $request = Yii::$app->request;
        $orderPenunjangForm = $request->post('OrderPenunjangForm');
        $tglKirimPasien = ArrayHelper::getValue($orderPenunjangForm, 'tgl_kirimpasien');
        if ($tglKirimPasien) {
            $tglKirimPasien = str_replace('/', '-', $tglKirimPasien);
            $tglKirimPasien = date('Y-m-d', strtotime($tglKirimPasien));
        }
        return [
            'programterapi_id' => ArrayHelper::getValue($orderPenunjangForm, 'programterapi_id'),
            'tgl_kirimpasien' => $tglKirimPasien,
            'pegawai_id' => ArrayHelper::getValue($orderPenunjangForm, 'pegawai_id'),
            'diagnosis' => ArrayHelper::getValue($orderPenunjangForm, 'diagnosis'),
            'frekuensi' => ArrayHelper::getValue($orderPenunjangForm, 'frekuensi_terapi'),
            'catatan' => ArrayHelper::getValue($orderPenunjangForm, 'catatan_dokterpengirim'),
        ];
    }

    public function run()
    {
        try {
            $request = Yii::$app->request;
            if ($request->method != 'POST') return $this->controller->responseJson(400, 'Must be post method');
            $id = Yii::$app->request->get('id');
            $decryptedId = DocoHelpers::decrypt($id);
            $model = new OrderPenunjangForm();
            $formName = 'OrderPenunjangForm';
            $model->attributes = $request->post($formName);
            if (!$model->validate()) {
                $response = $model->errors;
                $errors = DocoHelpers::parseError($response, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
            $parent = $this->getParentFromPayload();
            $childs = $this->getChildsFromPayload();
            if (empty($childs)) {
                $msgError = "Detail tindakan tidak boleh kosong !";
                return DocoHelpers::responseTemplate(422, 'Error', [], [
                    'title' => 'Peringatan!',
                    'text' => $msgError,
                    'message' => $msgError,
                ]);
            }
            $parent['orders'] = $childs;
            $programTerapiId = ArrayHelper::getValue($parent, 'programterapi_id');
            $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'informasi-program-fisioterapi-rajal/update-data',
                'method' => 'post',
                'payload' => [
                    'query' => [
                        'programterapi_id' => $programTerapiId
                    ],
                    'form_params' => $parent
                ],
                'with_metadata' => true
            ]);
            $response = DocoHelpers::response($response);
            return $response;
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}
