<?php

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\fisioterapi\models\SoapRanapForm;

class CpptAction extends BaseCurrentAction
{
    private function getListPegawaiTerapis()
    {
        $responseListPegawai = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => 'soap-ranap/get-terapis'
        ]);
        $listPegawai = ArrayHelper::map($responseListPegawai, 'pegawai_id', 'nama_pegawai');
        return $listPegawai;
    }

    private function getDataPasien($pendaftaranId)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => "soap-ranap/detail-patient",
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId
                ]
            ]
        ]);
        return ArrayHelper::getValue($response, 'data');
    }

    private function getDataProgramTerapis($programTerapiIdsString)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => "soap-ranap/get-program-terapis",
            'payload' => [
                'query' => [
                    'program_terapi_ids' => $programTerapiIdsString,
                ]
            ]
        ]);
        return ArrayHelper::getValue($response, 'data');
    }

    private function getParamDecrypted()
    {
        $paramGet = Yii::$app->request->get();
        $pendaftaranId = ArrayHelper::getValue($paramGet, 'pendaftaran_id');
        $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        $programTerapiIdsString = ArrayHelper::getValue($paramGet, 'program_terapi_ids');
        $programTerapiIds = explode(',', $programTerapiIdsString);
        $programTerapiIdsDecrypted = [];
        $tempProgramTerapiIdsString = "";
        foreach ($programTerapiIds as $key => $value) {
            $decryptedValue = DocoHelpers::decrypt($value);
            $programTerapiIdsDecrypted[] = $decryptedValue;
            $tempProgramTerapiIdsString = $tempProgramTerapiIdsString . $decryptedValue . ",";
        }
        $programTerapiIdsString = rtrim($tempProgramTerapiIdsString, ',');
        return [
            'pendaftaran_id' => $pendaftaranId,
            'programterapi_ids' => $programTerapiIdsDecrypted,
            'programterapi_ids_string' => $programTerapiIdsString,
        ];
    }

    public function run()
    {
        $request = Yii::$app->request;
        $paramGet = Yii::$app->request->get();
        $pendaftaranIdEnc = ArrayHelper::getValue($paramGet, 'pendaftaran_id');
        $programTerapiIdsStringEnc = ArrayHelper::getValue($paramGet, 'program_terapi_ids');
        $user = Yii::$app->user->getIdentity();
        $currentPegawai = ArrayHelper::getValue($user, 'id_pegawai');
        $this->_title = (new DocoHelpers)->coalesce(DHtml::getTitleMenu(), $this->_title);
        $model = new SoapRanapForm;
        $paramDecrypted = $this->getParamDecrypted();
        $pendaftaranId = ArrayHelper::getValue($paramDecrypted, 'pendaftaran_id');
        $programterapiIds = ArrayHelper::getValue($paramDecrypted, 'programterapi_ids');
        $programTerapiIdsString = ArrayHelper::getValue($paramDecrypted, 'programterapi_ids_string');
        $listPegawai = $this->getListPegawaiTerapis();
        $dataPasien = $this->getDataPasien($pendaftaranId);
        $dataProgramTerapis = $this->getDataProgramTerapis($programTerapiIdsString);
        $statusPeriksa = ArrayHelper::getValue($dataPasien, "status_periksa_id");
        $isReadOnlyParam = ArrayHelper::getValue($paramGet, 'readonly');
        $isReadonlyStatusPasienRanap = $statusPeriksa == DocoConstants::STATUS_RANAP_PULANG;
        $isReadOnly = $isReadOnlyParam || $isReadonlyStatusPasienRanap;
        $pasienAdmisiId = ArrayHelper::getValue($dataPasien, 'pasienadmisi_id');
        $instalasiId = DocoHelpers::encrypt(DocoConstants::INSTALASI_ID_RI);
        $daftarTindakan = "";
        $daftarTindakanArr = [];
        foreach ($dataProgramTerapis as $key => $value) {
            $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $programTerapiId = ArrayHelper::getValue($value, 'programterapi_id');
            $daftarTindakanArr[] = [
                'programterapi_id' => DocoHelpers::encrypt($programTerapiId),
                'daftartindakan_nama' => $daftarTindakanNama
            ];
        }
        $daftarTindakan = rtrim($daftarTindakan, ",");
        $dataView   = [
            'dataPasien' => $dataPasien,
            'daftarTindakanArr' => $daftarTindakanArr,
            'pendaftaranId' => $pendaftaranId,
            'pendaftaranIdEnc' => $pendaftaranIdEnc,
            'programTerapiIdsEnc' => $programTerapiIdsStringEnc,
            'model' => $model,
            'instalasiId' => $instalasiId,
            'listPegawai' => $listPegawai,
            'title' => $this->_title,
            'currentUser' => $currentPegawai,
            'pasienadmisiId' => $pasienAdmisiId,
            'isReadOnly' => $isReadOnly
        ];
        return $this->controller->renderAjax('cppt', compact('dataView'));
    }
}
