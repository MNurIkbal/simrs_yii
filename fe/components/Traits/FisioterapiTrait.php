<?php

namespace app\components\Traits;

use Yii;

use app\components\DocoConstants;
use app\modules\rajal\models\InstruksiPenunjangForm;

trait FisioterapiTrait
{
    public $type;
    public $serviceRest;
    public $mainUrl;
    public $frontendUrl;
    public $backendUrl;
    public $saveEndpoint;
    public $mappingPayloadFunc;

    private function setServiceFisioterapiByType()
    {
        $serviceRest = null;
        switch ($this->type) {
            case 'RJ':
                $serviceRest = Yii::$app->docoRest->rajal;
                $this->mainUrl = 'pemeriksaan';
                $this->frontendUrl = '/rajal/' . $this->mainUrl;
                // Perlu penyesuaian be
                // $this->backendUrl = 'tindakan-bmhp';
                // $this->saveEndpoint = $this->backendUrl . '/save-tindakan-bmhp';
                break;
            case 'RI':
                $serviceRest = Yii::$app->docoRest->ranap;
                $this->mainUrl = 'pemeriksaan-rawat-inap';
                $this->frontendUrl = '/ranap/' . $this->mainUrl;
                // Perlu penyesuaian be
                // $this->backendUrl = $this->mainUrl; 
                // $this->saveEndpoint = $this->mainUrl . '/cppt-create-tindakan';
                break;
            case 'RD':
                $serviceRest = Yii::$app->docoRest->igd;
                $this->mainUrl = 'pemeriksaan-igd';
                $this->frontendUrl = '/igd/' . $this->mainUrl;
                // Perlu penyesuaian be
                // $this->backendUrl = 'asesmen-dpjp';
                // $this->saveEndpoint = $this->backendUrl . '/dpjp-create-terapi-tindakan';
                break;
        }
        if (empty($serviceRest)) {
            return $this->responseJson(400, 'Service REST Fisioterapi is not set');
        }
        $this->serviceRest = $serviceRest;
    }

    public function actionFormModalFisioterapi($type)
    {
        // $this->setServiceFisioterapiByType();
        $inst_id = DocoConstants::INSTALASI_ID_RJ;
        $modelPenunjang = new InstruksiPenunjangForm;
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->session->get('user_identity');
        if ($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS) {
            $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
        }
        $url = [
            'form-action' => '/rajal/pemeriksaan/simpan-terapi-penunjang',
            'modal-pemeriksaan' => '/rajal/pemeriksaan/modal-pemeriksaan-penunjang?id=' . $id . '&instalasi_id=#instalasi_id#&ruangan_id=#ruangan_id#&kelaspelayanan_id=#kelaspelayanan_id#&penjamin_id=#penjamin_id#',
        ];
        $restFisioterapi = Yii::$app->docoRest->fisioterapi;
        $responseGetInstalasiFisio = $this->guzzleExec($restFisioterapi, [
            'url' => 'allow/get-instalasi-fisioterapi',
            'payload' => [
                'query' => []
            ]
        ]);
        $instalasiId = ArrayHelper::getValue($responseGetInstalasiFisio, 'data');
        $penjaminId = $this->_data_pasien['penjamin_id']; //get from PemeriksaanController@init
        $kelaspelayananId = $this->_data_pasien['kelaspelayanan_id']; //get from PemeriksaanController@init
        $wardDropdown = [];
        if (!empty($instalasiId)) {
            $wardData = $this->guzzleExec($this->_restRajal, [
                'url' => 'allow/get-list-ruangan',
                'payload' => [
                    'query' => [
                        'instalasi_id' => $instalasiId
                    ]
                ]
            ]);
            foreach ($wardData as $value)
                $wardDropdown[$value['ruangan_id']] = $value['ruangan_nama'];
        }
        $modelPenunjang->pegawai_id = Yii::$app->docoVars->user("kelompokpegawai_id") != DocoConstants::KELOMPOK_MEDIS ? $this->_data_pasien['pegawai_id'] : $this->_pegawai_id;
        $modelPenunjang->instalasi_id = $instalasiId;
        $modelPenunjang->pendaftaran_id = $this->helper->decrypt($id);
        $modelPenunjang->cppt_id = Yii::$app->request->get('cppt_id', null);
        $modelPenunjang->has_jadwal = 0;
        return $this->renderAjax('//cppt/fisioterapi/_modal', [
            'type' => $type,
            'user' => $user,
            'model' => $modelPenunjang,
            'wards' => $wardDropdown,
            'penjaminId' => $penjaminId,
            'kelaspelayananId' => $kelaspelayananId,
            'id' => $id,
            'url' => $url,
            'inst_id' => $inst_id
        ]);
    }
}
