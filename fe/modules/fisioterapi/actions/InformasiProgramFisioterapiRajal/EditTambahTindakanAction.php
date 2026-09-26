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

class EditTambahTindakanAction extends BaseCurrentAction
{
    private function getDatas($programTerapiId)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'tindakan-fisio/get-tindakan-rajal',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'programterapi_id' => $programTerapiId
                ],
                'form_params' => []
            ],
        ]);
        $responseDatas = ArrayHelper::getValue($response, 'data.data');
        return $responseDatas;
    }

    public function run()
    {
        try {
            $request = Yii::$app->request;
            $programTerapiIdEnc = $request->get('id');
            $programTerapiId = DocoHelpers::decrypt($programTerapiIdEnc);
            $tindakanDatas = $this->getDatas($programTerapiId);
            $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'informasi-program-fisioterapi-rajal/get-konfig-system',
                'method' => 'get',
            ]);
            $konfigApproveFisio = ArrayHelper::getValue($response, 'konfigApproveFisio');
            $result = [];
            foreach ($tindakanDatas as $key => $value) {
                $result[$value['jenispemeriksaanlab_nama']][] = $value;
            }
            $title = 'Tambah Terapi';
            $instalasiId = null;
            return $this->controller->renderAjax('edit-program-terapi/__modal_order_penunjang', [
                'result' => $result,
                'title' => $title,
                'konfigApproveFisio' => $konfigApproveFisio,
            ]);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}
