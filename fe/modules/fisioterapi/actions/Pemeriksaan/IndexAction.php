<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\modules\fisioterapi\models\SoapForm;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        $this->_rest = Yii::$app->docoRest->fisioterapi;

        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $programTerapiIds = $request->get('program_terapi_id');
        $programTerapiDetailIds = $request->get('program_terapi_detail_id');
        $isReadOnly = $request->get('readonly');

        $model = new SoapForm;
        $user = Yii::$app->user->getIdentity();
        $currentPegawai = ArrayHelper::getValue($user, 'id_pegawai');
        
        // Get Terapis
        $terapis = (new DocoHelpers)->guzzleExec($this->_rest, [
            'method' => 'GET',
            'url' => 'soap/get-terapis'
        ]);
        $listPegawai = ArrayHelper::map($terapis, 'pegawai_id', 'nama_pegawai');

        // Get Data Pasien
        $dataPasien = (new DocoHelpers)->guzzleExec($this->_rest, [
            'method' => 'GET',
            'url' => 'soap/detail-patient',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId
                ]
            ]
        ]);

        // Daftar Tindakan
        $getProgramTerapis = (new DocoHelpers)->guzzleExec($this->_rest, [
            'method' => 'GET',
            'url' => 'soap/get-program-terapis',
            'payload' => [
                'query' => [
                    'program_terapi_id' => $programTerapiIds
                ]
            ]
        ]);
        $dataProgramTerapis = ArrayHelper::getValue($getProgramTerapis, 'programTerapiDetails');
        $instalasiIdRj = ArrayHelper::getValue($getProgramTerapis, 'instalasiIdRjRj');

        $resultDaftarTindakan = [];
        foreach ($dataProgramTerapis as $value) {
            $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $programTerapiId = ArrayHelper::getValue($value, 'programterapi_id');
            $resultDaftarTindakan[] = [
                'daftartindakan_nama' => $daftarTindakanNama,
                'programterapi_id' => DocoHelpers::encrypt($programTerapiId)
            ];
        }
        $pasienIdEnc = DocoHelpers::encrypt(ArrayHelper::getValue($dataPasien, 'pasien_id'));
        $dataView = [
            'primaryKey' => $programTerapiIds,
            'dataPasien' => $dataPasien,
            'pendaftaranId' => $pendaftaranId,
            'programTerapiIds' => $programTerapiIds,
            'programTerapiDetailIds' => $programTerapiDetailIds,
            'model' => $model,
            'instalasiIdRj' => $instalasiIdRj,
            'listPegawai' => $listPegawai,
            'title' => $this->_title,
            'currentUser' => $currentPegawai,
            'pasienadmisiId' => ArrayHelper::getValue($dataPasien, 'pasienadmisi_id'),
            'isReadOnly' => $isReadOnly,
            'resultDaftarTindakan' => $resultDaftarTindakan,
            'pasienIdEnc' => $pasienIdEnc,
        ];

        return $this->controller->render('index', get_defined_vars());
    }
}
