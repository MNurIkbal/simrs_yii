<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\BodyMassIndex;
use Doco\components\DocoConstants;
use Yii;
use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\AnamnesaAdhy;
use Integrasi\Service\Sirs\Models\PasienV;
use app\modules\v1\models\AnamnesaDetail;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use app\modules\v1\models\PemeriksaanFisik;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LookupTransaksi;
use SirsCore\businessLogic\MonitoringTtvLogic;

class AsesmenKeperawatanController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\AsesmenKeperawatan';

    public $messageBroker = [
        'save-asesmen' => [
            'services' => [
                'SatuSehat' => [
                    'ObservationVitalSign' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                    ]
            ]
        ],
        'save-asesmen-adhy' => [
            'services' => [
                'SatuSehat' => [
                    'ObservationVitalSign' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["save-asesmen"] = ["POST"];
        $verbs["get-asesmen"] = ["GET"];

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);


        return $actions;
    }
    /**
     * function for handle show asesmen keperawatan data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetAsesmen()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'pendaftaran_id tidak boleh kosong!');
        }
        $model = Anamnesa::find()
            ->select([
                'dokter.nama_pegawai as dokter_nama',
                'perawat.nama_pegawai as perawat_nama',
                'suku.suku_nama as suku_nama',
                'anamnesa_t.*'
            ])
            ->leftJoin('pegawai_m as dokter', 'dokter.pegawai_id = anamnesa_t.pegawaidokter_id')
            ->leftJoin('pegawai_m as perawat', 'perawat.pegawai_id = anamnesa_t.pegawaiperawat_id')
            ->leftJoin('suku_m as suku', 'suku.suku_id = anamnesa_t.suku_id')
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();
        if (isset($model['pegawaiverifikasigizi_id']) && !empty($model['pegawaiverifikasigizi_id'])) {
            $model['pegawaiverifikasigizi_nama'] = Pegawai::find()->select(['nama_pegawai'])->where(['pegawai_id' => $model['pegawaiverifikasigizi_id']])->scalar();
        }
        if (isset($model['anamesa_id']) && !empty($model['anamesa_id'])) {
            $daftarDiagnosa = AnamnesaDetail::find()->select([
                'diagnosa_keperawatan',
                'tujuan_terukur'
            ])->where(['anamnesa_id' => $model['anamesa_id']])->asArray()->all();
            $model['daftarDiagnosa'] = $daftarDiagnosa;
        }
        if (empty($model)) {
            $model = Pendaftaran::find()->select([
                'pendaftaran_t.carabayar_id',
                'pendaftaran_t.penjamin_id',
                'carabayar_m.carabayar_nama',
                'penjamin_m.penjamin_nama',
                'lookuptransaksi_m.kode_transaksi'
            ])
                ->leftJoin('carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->leftJoin('penjamin_m', 'penjamin_m.penjamin_id = pendaftaran_t.penjamin_id')
                ->leftJoin("lookuptransaksi_m", "lookuptransaksi_m.kode_id = carabayar_m.carabayar_id AND lookuptransaksi_m.kode_fungsi = 'carabayar_m_carabayar_id'")
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()
                ->one();
        }


        return $this->responseJson(200, 'Data Asesmen Berhasil didapatkan', $model);
    }

    /**
     * function for get nama dokter
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetNamaDokter()
    {
        $request = Yii::$app->request;
        $pegawai_id = $request->get('pegawai_id', null);
        if (is_null($pegawai_id)) {
            return $this->responseJson(422, 'pegawai_id tidak boleh kosong!');
        }
        $model = Pegawai::find()
            ->select([
                'nama_pegawai'
            ])
            ->where(['pegawai_id' => $pegawai_id])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Nama Pegawai Berhasil didapatkan', $model);
    }

    /**
     * function for handle show pasien data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetPasien()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'pendaftaran_id tidak boleh kosong!');
        }
        $model = Pendaftaran::find()
            ->select([
                'pendidikan.pendidikan_nama as pendidikan_nama',
                'agama.lookup_name as agama',
                'darah.lookup_name as golongan_darah'
            ])
            ->leftJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
            ->leftJoin('pendidikan_m as pendidikan', 'pendidikan.pendidikan_id = pasien_m.pendidikan_id')
            ->leftJoin('lookup_m as agama', 'agama.lookup_id = CAST(pasien_m.agama AS INTEGER)')
            ->leftJoin('lookup_m as darah', 'darah.lookup_id = CAST(pasien_m.golongandarah AS INTEGER)')
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();
        $model['enable_pulang'] = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');
        return $this->responseJson(200, 'Data Pasien Berhasil didapatkan', $model);
    }

    /**
     * function for save asesmen keperawatan
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmen()
    {
        $request = Yii::$app->request;
        $dataKeperawatan = $request->post('formdata', []);

        $transaction = Yii::$app->db->beginTransaction();
        $pendaftaran_id = !isset($dataKeperawatan['pendaftaran_id']) || empty($dataKeperawatan['pendaftaran_id']) ? null : $dataKeperawatan['pendaftaran_id'];
        $anamesa_id = !isset($dataKeperawatan['anamesa_id']) || empty($dataKeperawatan['anamesa_id']) ? null : $dataKeperawatan['anamesa_id'];
        $model = Pendaftaran::find()
            ->select([
                'pasien_id'
            ])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();

        $inputAttribute = new Anamnesa;
        $inputAttribute->attributes = $dataKeperawatan;
        $inputAttribute->pasien_id = $model['pasien_id'];
        $inputAttribute->is_deleted = false;
        $inputAttribute->is_active  = true;
        $inputAttribute->created_date = date("Y-m-d H:i:s");

        $model = new Anamnesa;
        if (!is_null($anamesa_id)) {
            $model = Anamnesa::findOne($anamesa_id);
        }
        $model->attributes = $inputAttribute->attributes;
        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
        
        MonitoringTtvLogic::feedData($dataKeperawatan, DocoConstants::ASESMEN_KEPERAWATAN, DocoConstants::INSTALASI_RAWAT_JALAN);
        
        $anamesa_id = $model->getPrimaryKey();
        AnamnesaDetail::deleteAll('anamnesa_id=:anamnesa_id', [':anamnesa_id' => $anamesa_id]);
        if (isset($dataKeperawatan['daftarDiagnosa']) && !empty($dataKeperawatan['daftarDiagnosa'])) {
            $detailKeperawatan = [];
            foreach ($dataKeperawatan['daftarDiagnosa'] as $index => $item) {
                $dataKeperawatan['daftarDiagnosa'][$index]['anamnesa_id'] = $anamesa_id;
            }
            try {
                AnamnesaDetail::batchInsert($dataKeperawatan['daftarDiagnosa']);
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        }
        if (!$this->saveDefaultPemeriksaanFisik($model)) {
            // $transaction->rollBack();
            // return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Keperawatan Berhasil!', [
            'status_merokok' => $model->ketergantungan_jenis != '' ? in_array('rokok', explode(',', $model->ketergantungan_jenis)) : false,
            'riwayat_penyakit_keluarga_list' => $model->riwayat_penyakit_keluarga_list == '' ? '-' : $model->riwayat_penyakit_keluarga_list,
            'status_ekonomi' => $model->status_ekonomi != '' ? ucwords(str_replace('_', ' ', $model->status_ekonomi)) : '-',
            'pendaftaran_id' => $model->pendaftaran_id,
            'vital-sign' => [
                'pulse' => 90,
                'breathing' => 80,
                'sistol' => 80,
                'diastole' => 110,
                'temp' => 36
            ]
        ]);
    }

    /**
     * function for save asesmen keperawatan
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmenAdhy()
    {
        $request = Yii::$app->request;
        $dataKeperawatan = $request->post('formdata', []);

        $transaction = Yii::$app->db->beginTransaction();
        $pendaftaran_id = !isset($dataKeperawatan['pendaftaran_id']) || empty($dataKeperawatan['pendaftaran_id']) ? null : $dataKeperawatan['pendaftaran_id'];
        $anamesa_id = !isset($dataKeperawatan['anamesa_id']) || empty($dataKeperawatan['anamesa_id']) ? null : $dataKeperawatan['anamesa_id'];
        $model = Pendaftaran::find()
            ->select([
                'pasien_id'
            ])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();

        $inputAttribute = new AnamnesaAdhy;
        $inputAttribute->attributes = $dataKeperawatan;
        $inputAttribute->pasien_id = $model['pasien_id'];
        $inputAttribute->is_deleted = false;
        $inputAttribute->is_active  = true;
        $inputAttribute->created_date = date("Y-m-d H:i:s");

        $model = new AnamnesaAdhy;
        if (!is_null($anamesa_id)) {
            $model = AnamnesaAdhy::findOne($anamesa_id);
        }
        $model->attributes = $inputAttribute->attributes;
        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
        $anamesa_id = $model->getPrimaryKey();
        AnamnesaDetail::deleteAll('anamnesa_id=:anamnesa_id', [':anamnesa_id' => $anamesa_id]);
        if (isset($dataKeperawatan['daftarDiagnosa']) && !empty($dataKeperawatan['daftarDiagnosa'])) {
            $detailKeperawatan = [];
            foreach ($dataKeperawatan['daftarDiagnosa'] as $index => $item) {
                $dataKeperawatan['daftarDiagnosa'][$index]['anamnesa_id'] = $anamesa_id;
            }
            try {
                AnamnesaDetail::batchInsert($dataKeperawatan['daftarDiagnosa']);
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        }
        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Keperawatan Berhasil!', [
            'status_merokok' => $model->ketergantungan_jenis != '' ? in_array('rokok', explode(',', $model->ketergantungan_jenis)) : false,
            'riwayat_penyakit_keluarga_list' => $model->riwayat_penyakit_keluarga_list == '' ? '-' : $model->riwayat_penyakit_keluarga_list,
            'status_ekonomi' => $model->status_ekonomi != '' ? ucwords(str_replace('_', ' ', $model->status_ekonomi)) : '-',
            'pendaftaran_id' => $model->pendaftaran_id,
            'vital-sign' => [
                'pulse' => 90,
                'breathing' => 80,
                'sistol' => 80,
                'diastole' => 110,
                'temp' => 36
            ]
        ]);
    }

    protected function saveDefaultPemeriksaanFisik(Anamnesa $anamnesa)
    {
        $model = PemeriksaanFisik::find()->where([
            'pendaftaran_id' => $anamnesa->pendaftaran_id,
            'pasien_id' => $anamnesa->pasien_id
        ])->one();
        $modelPasien = PasienV::find()->where([
            'pasien_id' => $anamnesa->pasien_id
        ])->one();
        
        if($model && $model->dokter_id!=null){
            return true;
        }
      
        if (!$model) {
            $model = new PemeriksaanFisik;  
        }

            $model->pendaftaran_id = $anamnesa->pendaftaran_id;
            $model->pasien_id = $anamnesa->pasien_id;
            $model->pasienadmisi_id = $anamnesa->pasienadmisi_id;
            $model->pegawaiperawat_id = $anamnesa->pegawaiperawat_id;
            // $model->dokter_id = $anamnesa->pegawaidokter_id;
            $model->tglperiksafisik = $anamnesa->tgl_anamnesis;
            if ($anamnesa->td) {
                $model->tekanandarah = $anamnesa->td;
                $td = explode("/", $anamnesa->td);
                $model->td_systolic = isset($td[0]) && is_int($td[0]) ? $td[0] : null;
                $model->td_diastolic = isset($td[1]) && is_int($td[1]) ? $td[1] : null;
            }
           
            $model->riwayat_penyakit_dahulu = $anamnesa->riwayat_penyakit_nama;
            $model->riwayat_penyakit_keluarga = $anamnesa->riwayat_penyakit_keluarga_list; 
                    
            $model->detaknadi = is_int($anamnesa->nadi) ? $anamnesa->nadi : null;
            $model->suhutubuh = is_int($anamnesa->suhu) ? $anamnesa->suhu : null;
            $model->beratbadan_kg = is_int($anamnesa->berat_badan) ? $anamnesa->berat_badan : null;
            $model->tinggibadan_cm = is_int($anamnesa->tinggi_badan) ? $anamnesa->tinggi_badan : null;
            $model->pernapasan = $anamnesa->rr;
            $model->keadaanumum = $anamnesa->keluhan_utama;
            $tinggi_badan_dalam_meter_kuadrat = (($model->tinggibadan_cm/100) * ($model->tinggibadan_cm/100));
            $model->imt = ($model->beratbadan_kg / ($tinggi_badan_dalam_meter_kuadrat == 0 ? 1 : $tinggi_badan_dalam_meter_kuadrat));
            if ($modelPasien->jenis_kelamin == 'Laki-laki'){
                $model->bb_ideal = floatval(($model->tinggibadan_cm - 100) - (0.1 * ($model->tinggibadan_cm-100)));
            }else{
                $model->bb_ideal = floatval(($model->tinggibadan_cm - 100) - (0.15 * ($model->tinggibadan_cm-100)));
            }            
            if (!$model->save()) {
                Yii::error([
                    'save_default_pemeriksaan_fisik_error-data' => $model->getErrors()
                ]);
                return false;
            }
        
        return true;
    }

    public function actionGetRuanganSpesialis()
    {
        $request = Yii::$app->request;
        $ruanganId = $request->get('ruangan_id');
        $lookup = LookupTransaksi::find()->select(['kode_transaksi'])
            ->where(['kode_id' => $ruanganId, 'kode_singkatan' => 'SP'])->one();

        return ArrayHelper::getValue($lookup, 'kode_transaksi');
    }

    private function getPeriksaFisik($pendaftaranId)
    {
        return PemeriksaanFisik::find()->where(['pendaftaran_id' => $pendaftaranId])->one();

    }
}
