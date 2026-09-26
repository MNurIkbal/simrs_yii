<?php

namespace Doco\fisioterapi\actions\InformasiProgramFisioterapiRanap;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\fisioterapi\models\OrderPenunjangForm;

class EditAction extends BaseCurrentAction
{
    private function getDataDetail($programTerapiId)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'informasi-program-fisioterapi-ranap/get-data-detail',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'programterapi_id' => $programTerapiId
                ],
                'form_params' => []
            ],
        ]);
        $responseDatas = ArrayHelper::getValue($response, 'data');
        return $responseDatas;
    }

    private function getChoosedDatas($programTerapiId)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'tindakan-fisio/get-tindakan-terpilih',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'programterapi_id' => $programTerapiId
                ],
                'form_params' => []
            ],
        ]);
        $responseDatas = ArrayHelper::getValue($response, 'data.data');
        $i = 0;
        foreach ($responseDatas as $key => $value) {
            $responseDatas[$key]['index'] = $i;
            $i++;
        }
        return $responseDatas;
    }

    private function getChoosedDatasPaket($programTerapiId)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'tindakan-fisio/get-tindakan-paket-terpilih',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'programterapi_id' => $programTerapiId
                ],
                'form_params' => []
            ],
        ]);
        $responseDatas = ArrayHelper::getValue($response, 'data.data');
        $i = 0;
        foreach ($responseDatas as $key => $value) {
            $responseDatas[$key]['index'] = $i;
            $i++;
        }
        return $responseDatas;
    }

    public function run()
    {
        try {
            $model = new OrderPenunjangForm;
            $id = Yii::$app->request->get('id');
            $paket = Yii::$app->request->get('p');
            $decryptedId = DocoHelpers::decrypt($id);
            $url = [
                "form-action" => "/fisioterapi/informasi-program-fisioterapi-ranap/update-data",
                "modal-pemeriksaan" => "/fisioterapi/informasi-program-fisioterapi-ranap/edit-tambah-tindakan?id=$id&"
            ];
            $userIdentity = Yii::$app->session->get('user_identity');
            $loginPemakaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
            $parentData = $this->getDataDetail($decryptedId);
            $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'informasi-program-fisioterapi-ranap/get-konfig-system',
                'method' => 'get',
            ]);
            $konfigApproveFisio = ArrayHelper::getValue($response, 'konfigApproveFisio');
            $model->programterapi_id = $decryptedId;
            $model->frekuensi_terapi = ArrayHelper::getValue($parentData, 'frekuensi');
            $model->diagnosis = ArrayHelper::getValue($parentData, 'diagnosa');
            $model->catatan_dokterpengirim = ArrayHelper::getValue($parentData, 'catatan');
            $model->pegawai_id = $loginPemakaiId;
            $dataDetail = ArrayHelper::getValue($parentData, 'details');
            $dataEditDetail = ArrayHelper::getValue($parentData, 'detailsPerubahan');
            $parentDetails = !empty($dataEditDetail) ? $dataEditDetail['programTerapiDetailApprove'] : $dataDetail;
            if($paket == true){
                $choosedDatas =  $this->getChoosedDatasPaket($decryptedId);
            }else{
                $choosedDatas = $this->getChoosedDatas($decryptedId);
            }
            foreach ($choosedDatas as $key => $value) {
                $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                $parentDetailKey = DocoHelpers::searchArray($daftarTindakanId, 'daftartindakan_id', $parentDetails);
                if($paket == true){
                    $catatanChild = ArrayHelper::getValue($parentData, "$key.catatan");
                }else{
                    $catatanChild = ArrayHelper::getValue($parentDetails, "$parentDetailKey.catatan");
                }
                $daftarTindakanNama = ArrayHelper::getValue($parentDetails, "$parentDetailKey.daftartindakan_nama");
                $programterapi_deleted = ArrayHelper::getValue($parentDetails, "$parentDetailKey.programterapi_deleted", false);
                $is_approve_edit = ArrayHelper::getValue($parentDetails, "$parentDetailKey.is_approve_edit", null);
                $choosedDatas[$key]['catatan'] = $catatanChild;
                $choosedDatas[$key]['programterapi_deleted'] = $programterapi_deleted;
                $choosedDatas[$key]['is_approve_edit'] = $is_approve_edit;
            }
            $diagPenyerta = ArrayHelper::getValue($parentData, 'a_diag_penyerta');
            return $this->controller->renderAjax('edit-program-terapi/__modal', [
                'title' => 'Ubah Order Fisioterapi',
                'id' => $id,
                'url' => $url,
                'model' => $model,
                'type' => 'Fisioterapi',
                'user' => null,
                'penjaminId' => null,
                'kelaspelayananId' => null,
                'choosedDatas' => $choosedDatas,
                'diagPenyerta' => $diagPenyerta,
                'konfigApproveFisio' => $konfigApproveFisio,
            ]);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}
