<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 * @Mod: Ardi Pratama Septiadi
 */

namespace app\modules\v1\controllers;

use app\modules\v1\cache\Cache;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\KeluargaPasien;
use app\modules\v1\models\PasienPulangRdRjView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\payload\Kunjungan;
use app\modules\v1\payload\PasienAdmisi as PasienAdmisiPayload;
use app\modules\v1\payload\Pasien as PayloadPasien;
use app\modules\v1\payload\BpjsNewForm;
use app\modules\v1\payload\KeluargaPasienForm;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\Pegawai;
use app\modules\v1\components\Penomoran;
use app\modules\v1\payload\MultiCarabayar as PayloadMultiPayer;

use app\modules\v1\payload\Rujukan as PayloadRujukan;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\PjPasien;
use app\modules\v1\payload\TipePasien;
use SirsCore\features\IntegrasiAkunting;
use app\modules\v1\components\BpjsController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Yii;
use app\modules\v1\models\TarifTotalRsFn;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;
use app\modules\v1\payload\PenanggungBiayaForm;
use app\modules\v1\components\IdentifyUser;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\TarifTotalKamarRsFn;
use yii\helpers\ArrayHelper;
use Doco\components\constans\BpjsConstans;
use Doco\components\DocoConstansId;
use Doco\models\bpjs\BpjsAplicare;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Traits\AutoPlafonTrait;
use app\modules\v1\models\CaraBayar;

class PendaftaranRanapController extends BpjsController
{
    use AutoPlafonTrait;

    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    const PENOMORAN_RI = 194;
    const ATTEMPT = 1;
    const PENOMORAN_PASIEN = 190;

    /*
    *
    * @var prosesError
    *
    */
    protected $prosesError = 0;

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        $verbs["save-pendaftaran"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * this function to store newest data ranap
     *
     * @param Array $payload Payload from request
     * @return JSON
     **/
    public function actionSavePendaftaran()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $konfig = Cache::getKonfigSystem();
        $isBpjs = $isMultiPayer = false;

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $listTagihan = [];
        /** allow Bpjs */
        $allowBpjs = $request->post('allow_bpjs',false);
        /**
         * @var app\modules\v1\payload\PjPasien
         */
        $pjPasien = [];

        /**
         * @var app\modules\v1\payload\BpjsNewForm
         */
        $bpjs = [];
        $payloadBpjs = [];

        /**
         * @var app\modules\v1\payload\PayloadRujukan
         */
        $rujukan = [];

        /**
         * @var app\modules\v1\payload\AsuransiForm
         */
        $asuransi = [];
        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $payloadPasienAdmisi = new PasienAdmisiPayload;
        $payloadMultipayer = new PayloadMultiPayer;
        $payloadPasienAdmisi->scenario = "default";
        $payloadPasienAdmisi->attributes = $post['pasien_admisi'];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payloadKunjungan->is_ranap = true;
        $payloadPasienAdmisi->keterangan = $payloadPasienAdmisi->keterangan;
        $payloadMultipayer->isMultipayer = $isMultiPayer;

        // Set penanggung jawab if exist
        if (!empty($post['pj_pasien'])) {
            $payloadPjPasien = new PjPasien;
            $payloadPjPasien->attributes = $post['pj_pasien'];
            $payloadPjPasien->pj_tanggal_lahir = !empty($payloadPjPasien->pj_tanggal_lahir) ? date('Y-m-d', strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            $pjPasien = $payloadPjPasien->attributes;
            if (!$payloadPjPasien->validate()) {
                $errorParse['pj_pasien'] = $payloadPjPasien->errors;
            }

        }

        // condition if pendaftaran bbl it will create new pasien record
        $isBbl = false;
        if (isset($post['is_bbl'])) {
            $isBbl = true;
            $pasienPayLoad = new PayloadPasien;
            $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
            $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
            $pasienPayLoad->is_aps = false;
            // 100 jenis identitas lainnya
            $pasienPayLoad->jenisidentitas = '100';
            $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir) ? date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;
            if (!$pasienPayLoad->validate()) {
                $errorParse['pasien'] = $pasienPayLoad->errors;
            }

        }
        /** Validate Form */
        if (!$payload->validate()) {
            $errorParse['tipe_pasien'] = $payload->errors;
        }

        if (!$payloadKunjungan->validate()) {
            $errorParse['kunjungan'] = $payloadKunjungan->errors;
        }

        if (!$payloadPasienAdmisi->validate()) {
            $errorParse['pasien_admisi'] = $payloadPasienAdmisi->errors;
        }

        // Set asuransi
        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->carabayar_id = $payload->carabayar_id;
            $payloadAsuransi->penjamin_id = $payload->penjamin_id;
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? $payloadAsuransi->tgl_konfirmasi : null;
            $payloadAsuransi->penjamingrade_id = isset($payloadAsuransi['penjamingrade_id']) && !empty($payloadAsuransi['penjamingrade_id']) ? $payloadAsuransi['penjamingrade_id'] : null;
            $asuransi = $payloadAsuransi->attributes;
            if (!$payloadAsuransi->validate()) $errorParse['asuransi'] = $payloadAsuransi->errors;
        }
        if (!empty($post['bpjs'])) {
            $payloadBpjs = new BpjsNewForm;
            $payloadBpjs->attributes = $post['bpjs'];
            $payloadBpjs->no_rekam_medik = $payload->no_rekam_medik;

             /** Determine patient is upgrade class or not */
             $kp = KelasPelayanan::find()
             ->select(['bpjs_kelas','kelas_rawat_naik_bpjs'])
             ->where([
                 'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id
             ])
             ->one();

            $kelasNaik = ArrayHelper::getValue($kp, 'kelas_rawat_naik_bpjs');
            $kp = ArrayHelper::getValue($kp, 'bpjs_kelas');
            // if(!empty($kp) && !empty($kelasNaik)) {
            //     if((int) $kp < (int) $payloadBpjs->kelas_rawat) {
            //         $listKelasNaik = $this->lookup_type->actionGetLookupType('kelas_rawat_naik_bpjs');
            //         if(!empty($listKelasNaik) && !empty($kelasNaik)) {
            //             foreach($listKelasNaik as $val) {
            //                 if($val['lookup_id'] == $kelasNaik) {
            //                     $mappedKelasNaik = $val['lookup_value'];
            //                     break;
            //                 }
            //             }
            //         }
            //
            //         if(!isset($mappedKelasNaik)) {
            //             return [
            //                 'status' => 422,
            //                 'title' => 'Proses Gagal!',
            //                 'text' => 'Kelas yang dipilih belum di mapping dengan kelas naik bpjs!.',
            //             ];
            //         }
            //
            //         $payloadBpjs->klsRawatNaik = (int) $mappedKelasNaik;
            //         $payloadBpjs->pembiayaan = "1"; //@todo masih hardcode
            //         $payloadBpjs->penanggungJawab = "Pribadi"; //@todo masih hardcode
            //     }
            // }

            if (ArrayHelper::getValue($post, 'bpjs.is_naikkelas_ranap', null) == true) {
                $payloadBpjs->klsRawatNaik = ArrayHelper::getValue($post, 'bpjs.naik_kelas_rawat_inap');
                $payloadBpjs->pembiayaan = ArrayHelper::getValue($post, 'bpjs.pembiayaan');; //@todo masih hardcode
                $payloadBpjs->penanggungJawab = ArrayHelper::getValue($post, 'bpjs.nama_penganggung_jawab');; //@todo masih hardcode
            }
            $isBpjs = true;

            /** Statis Rujukan WIP */
            $diagnosa = Diagnosa::find()->select([
                'diagnosa_id'
            ])->where([
                'diagnosa_kode' => $payloadBpjs->diagnosa_awal
            ])->one();


            $payload->asalrujukan_id = 2;
            $post['rujukan'] = [
                'rujukandari_id' => 1,
                'no_rujukan' => $payloadBpjs->no_rujukan ? $payloadBpjs->no_rujukan : $payloadBpjs->no_kartu,
                'nama_perujuk' => 'BPJS',
                'tanggal_rujukan' => date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan)),
                'kodediagnosa_rujukan' => $payloadBpjs->diagnosa_awal,
                'diagnosa_id' => !empty($diagnosa->diagnosa_id) ? $diagnosa->diagnosa_id : null,
            ];
            if($allowBpjs) {
                $payloadBpjs->scenario = 'unauth';
            }
            if (!$payloadBpjs->validate()) $errorParse['bpjs'] = $payloadBpjs->errors;
        }

        /** Set Rujukan */
        if (!empty($post['rujukan'])) {
            $payloadRujukan = new PayloadRujukan;
            $payloadRujukan->attributes = $post['rujukan'];
            $payloadRujukan->asalrujukan_id = $payload->asalrujukan_id;
            $payloadRujukan->tanggal_rujukan = date('Y-m-d', strtotime($payloadRujukan->tanggal_rujukan));
            if (!$payloadRujukan->validate()) $errorParse['rujukan'] = $payloadRujukan->errors;
            $rujukan = $payloadRujukan->attributes;
        }

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse,
            ];
        }
        /* start checking accomodation rates  */
        // define params yg dibutuhin buat filter data kamar, kondisi kalau pasien bukan titipan
        $kamarParams = [
            $payloadKunjungan->ruangan_id,
            $payload->penjamin_id,
            $post['kelaspelayanan_selected'],
            'kamar'
        ];
        $kamarRuanganId = $payloadPasienAdmisi->kamarruangan_id;
        $tempatTidurId = $payloadPasienAdmisi->kamartempattidur_id;

        //params yg dibutuhkan ketika kondisi pasien adalah pasien titipan
        if ( $payloadPasienAdmisi->is_pasientitipan ) {
            $kamarParams = [
                $payloadPasienAdmisi->ruangan_titipan_id,
                $payload->penjamin_id,
                $payloadPasienAdmisi->kelas_ditagihkan_id,
                'kamar'
            ];
            $kamarRuanganId = $payloadPasienAdmisi->kamar_titipan_id;
            $tempatTidurId = $payloadPasienAdmisi->tempattidur_titipan_id;
        }

        // recheck tarif akomodasi kamar
        $ruanganAkomodasiRecord = (new TarifTotalKamarRsFn([
            'extParam' => $kamarParams
        ]))->find()->andWhere([
            'kamarruangan_id' => $kamarRuanganId,
            'kamartempattidur_id' => $tempatTidurId
        ])->one();

        // return error ketika tarif akomodasi kamar tidak ditemukan
        if (empty($ruanganAkomodasiRecord)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Tarif akomodasi pada ruangan yang dipilih belum tersedia.',
            ];
        }
        /* end */

        $pendaftaranAsalId = $latestMedicalRecord = null;
        if (!$isBbl) {
            // get pasien record by no rekam medik
            $pasienRecord = Pasien::find()->where([
                'no_rekam_medik' => $payload->no_rekam_medik,
            ])->select(['pasien_id', 'tanggal_lahir'])->asArray()->one();
            if (empty($pasienRecord)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien tidak ditemukan',
                ];
            }

            // Checking existing active rd or rj record
            $latestMedicalRecord = PasienPulangRdRjView::find()
                ->andWhere([
                    'pasien_id' => $pasienRecord['pasien_id'],
                ])
                ->select([
                    'pendaftaran_id',
                    'is_ranap',
                    'pasienadmisi_id',
                    'instalasi_id',
                ])
                ->orderBy('pasienpulangrdrj_v.tgl_pendaftaran DESC')
                ->asArray()
                ->one();
            if (empty($latestMedicalRecord)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Riwayat Rawat Darurat / Rawat Jalan Tidak Ditemukan',
                ];
            } else if (!empty($latestMedicalRecord) && (!empty($latestMedicalRecord['pasienadmisi_id']))) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Riwayat ' . $latestMedicalRecord['instalasi_id'] === DocoConstants::INST_ID_RJ ? 'Rawat Jalan' : 'Rawat Darurat' . ' Telah terdaftar sebagai rawat inap',
                ];
            }
            $pendaftaranAsalId = $latestMedicalRecord['pendaftaran_id'];
        } else {
            $pasienRecord = [
                'pasien_id' => null,
                'tanggal_lahir' => $pasienPayLoad->tanggal_lahir,
            ];
        }
        //** MultiPayer */
        if($payload->is_multi_payer == true && !empty($post['multi_payer'])) {
            $dataMultiPayer = $post['multi_payer'];
            $payloadMultipayer->attributes = $dataMultiPayer;
            $payloadMultipayer->isMultipayer = true;
            $payloadMultipayer->add_tgl_konfirmasi_1 = !empty($payloadMultipayer->add_tgl_konfirmasi_1) ? date('Y-m-d', strtotime($payloadMultipayer->add_tgl_konfirmasi_1)) : null;
            $payloadMultipayer->add_namapemilikasuransi_1 = isset($dataMultiPayer['add_namapemilikasuransi_1']) && !empty($dataMultiPayer['add_namapemilikasuransi_1']) ? $dataMultiPayer['add_namapemilikasuransi_1'] : null;
            $payloadMultipayer->add_nomorpokokperusahaan_1 = isset($dataMultiPayer['add_nomorpokokperusahaan_1']) && !empty($dataMultiPayer['add_nomorpokokperusahaan_1']) ? $dataMultiPayer['add_nomorpokokperusahaan_1'] : null;
            $payloadMultipayer->add_penjamingrade_id_1 = isset($dataMultiPayer['add_penjamingrade_id_1']) && !empty($dataMultiPayer['add_penjamingrade_id_1']) ? $dataMultiPayer['add_penjamingrade_id_1'] : null;
            if(isset($payloadMultipayer->is_add_payer) && $payloadMultipayer->is_add_payer == 'true') {
                $payloadMultipayer->add_tgl_konfirmasi_2 = !empty($payloadMultipayer->add_tgl_konfirmasi_2) ? date('Y-m-d', strtotime($payloadMultipayer->add_tgl_konfirmasi_2)) : null;
                $payloadMultipayer->add_namapemilikasuransi_2 = isset($dataMultiPayer['add_namapemilikasuransi_2']) && !empty($dataMultiPayer['add_namapemilikasuransi_2']) ? $dataMultiPayer['add_namapemilikasuransi_2'] : null;
                $payloadMultipayer->add_nomorpokokperusahaan_2 = isset($dataMultiPayer['add_nomorpokokperusahaan_2']) && !empty($dataMultiPayer['add_nomorpokokperusahaan_2']) ? $dataMultiPayer['add_nomorpokokperusahaan_2'] : null;
                $payloadMultipayer->add_penjamingrade_id_2 = isset($dataMultiPayer['add_penjamingrade_id_2']) && !empty($dataMultiPayer['add_penjamingrade_id_2']) ? $dataMultiPayer['add_penjamingrade_id_2'] : null;
            } else {
                $payloadMultipayer->add_carabayar_id_2 = null;
                $payloadMultipayer->add_penjamin_id_2 = null;
                $payloadMultipayer->add_asalrujukan_id_2 = null;
                $payloadMultipayer->add_penjamingrade_id_2 = null;
            }
            if (!$payloadMultipayer->validate()) $errorParse['multi_payer'] = $payloadMultipayer->errors;
            $isMultiPayer = true;
        } else {
            $payloadMultipayer->add_carabayar_id_1 = null;
            $payloadMultipayer->add_penjamin_id_1 = null;
            $payloadMultipayer->add_asalrujukan_id_1 = null;
            $payloadMultipayer->add_carabayar_id_2 = null;
            $payloadMultipayer->add_penjamin_id_2 = null;
            $payloadMultipayer->add_asalrujukan_id_2 = null;
            $payloadMultipayer->add_penjamingrade_id_1 = null;
            $payloadMultipayer->add_penjamingrade_id_2 = null;
        }

        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis);
        $detailTindakan = array();
        $idRuangan = ($payloadPasienAdmisi->is_pasientitipan) ? $payloadPasienAdmisi->ruangan_titipan_id : $payloadKunjungan->ruangan_id;
        $idKelasPelayanan =  ($payloadPasienAdmisi->is_pasientitipan) ? $payloadPasienAdmisi->kelas_ditagihkan_id : $payloadKunjungan->kelaspelayanan_id;

        if (!empty($tindakanKarcis)) {
            $tarifTotal = (new TarifTotalRsFn([
                'extParam' => [
                    $idRuangan,
                    $payload->penjamin_id,
                    $idKelasPelayanan,
                    'pelayanan'
                ]
            ]));
            $docoContantsId = new DocoConstansId();
            $kelompok_tindakan_ids = $docoContantsId->actionGetAdditional(DocoConstants::KELOMPOK_TINDAKAN_ID, true);
            $modelTarifTotal = $tarifTotal::find()
            ->andWhere([
                'daftartindakan_id' => $tindakanKarcis
            ]);
            if(!empty($kelompok_tindakan_ids)){
                foreach ($kelompok_tindakan_ids as $value){
                    if(isset($value['operand']) || !empty($value['operand'])){
                        $modelTarifTotal->andWhere([$value['operand'], $value['column'], $value['value']]);
                    }else{
                        $modelTarifTotal->andWhere([$value['column'] => $value['value']]);
                    }
                }
            }
            $modelTarifTotal = $modelTarifTotal->all();

            if (!empty($modelTarifTotal)) {
                foreach ($modelTarifTotal as $value) {
                    if($value['dokter_id'] == $payloadKunjungan->dokter_id) {
                        $detailTindakan[$value['daftartindakan_id']] = [
                            'dokter_id' => $payloadPasienAdmisi->pegawai_id,
                            'perawat_id' => null,
                            'perawat2_id' => null,
                            'tipepaket_id' => null,
                            'is_cyto' => false,
                            'is_penyulit' => false,
                            'qty' => 1,
                            'daftartindakan_id' => $value->daftartindakan_id,
                            'implementasi_id' => null,
                            'instruksitindakan_id' => null
                        ];
                    } else {
                        if(!isset($detailTindakan[$value['daftartindakan_id']]) && $value['dokter_id'] == null) {
                            $detailTindakan[$value['daftartindakan_id']] = [
                                'dokter_id' => $payloadPasienAdmisi->pegawai_id,
                                'perawat_id' => null,
                                'perawat2_id' => null,
                                'tipepaket_id' => null,
                                'is_cyto' => false,
                                'is_penyulit' => false,
                                'qty' => 1,
                                'daftartindakan_id' => $value->daftartindakan_id,
                                'implementasi_id' => null,
                                'instruksitindakan_id' => null
                            ];
                        }
                    }
                }
            }
        }

        // Mapping golongan
        $getTotalhari = DocoHelpers::convertToHari($pasienRecord['tanggal_lahir']);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;
        foreach ($getGolongan as $value) {
            if ($value['golonganumur_minimal'] <= $getTotalhari
                && $value['golonganumur_maksimal'] >= $getTotalhari) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }
        $dataCaraBayar = CaraBayar::findOne($payload->carabayar_id);
        $groupCaraBayarId = ArrayHelper::getValue($dataCaraBayar, 'groupcarabayar_id');
        $limitTagihan = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;
        if ($isBpjs || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon(DocoConstants::INST_ID_RI, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }
        
        $additionals = [
            'tarif' => $listTagihan,
            'penanggung_jawab' => $pjPasien,
            'pasien_admisi' => [
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'pasien_id' => $pasienRecord['pasien_id'],
                'kamarruangan_id' => $payloadPasienAdmisi->kamarruangan_id,
                'kamartempattidur_id' => $payloadPasienAdmisi->kamartempattidur_id,
                'kelaspelayanan_id' => !empty($post['kelaspelayanan_selected']) ? $post['kelaspelayanan_selected'] : $payloadKunjungan->kelaspelayanan_id,
                'pegawai_id' => $payloadPasienAdmisi->pegawai_id,
                'tgl_admisi' => date("Y-m-d H:i:s", strtotime($payloadPasienAdmisi->tgl_admisi)),
                'tgl_pendaftaran' => date("Y-m-d H:i:s"),
                'kunjungan' => DocoConstants::VAR_K_L,
                'bpjs_id' => null,
                'status_ranap' => DocoConstants::STATUS_RANAP_BELUM_PERIKSA,
                'is_pasientitipan' => ($payloadPasienAdmisi->is_pasientitipan == 1) ? true : false,
                'is_aps' => ($payloadPasienAdmisi->is_aps == 1) ? true : false,
                'carabayar_id' => $payload->carabayar_id,
                'penjamin_id' => $payload->penjamin_id,
                'kamar_titipan_id' => $payloadPasienAdmisi->kamar_titipan_id ? (int)$payloadPasienAdmisi->kamar_titipan_id : null,
                'kelas_ditagihkan_id' => $payloadPasienAdmisi->kelas_ditagihkan_id ? (int)$payloadPasienAdmisi->kelas_ditagihkan_id : null,
                'ruangan_titipan_id' => $payloadPasienAdmisi->ruangan_titipan_id ? (int)$payloadPasienAdmisi->ruangan_titipan_id : null,
                'limit_tagihan' => $limitTagihan
            ],
            'masuk_kamar' => [
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'pegawai_id' => $payloadPasienAdmisi->pegawai_id,
                'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
                'kamartempattidur_id' => $payloadPasienAdmisi->kamartempattidur_id,
                'kamarruangan_id' => $payloadPasienAdmisi->kamarruangan_id,
                'tgl_masukkamar' => date("Y-m-d", strtotime($payloadPasienAdmisi->tgl_admisi)),
                'jam_masukkamar' => date("H:i:s", strtotime($payloadPasienAdmisi->tgl_admisi)),
            ],
            'asuransi' => $asuransi,
            'pendaftaranasal_id' => $pendaftaranAsalId,
            'rujukan' => $rujukan,
            'additional_payer' => $payloadMultipayer
        ];

        if ($isBbl) {
            $additionals = array_merge($additionals, [
                'pendaftaran_ibu_id' => $post['pendaftaran_ibu_id'],
                'kelahiran_id' => $post['kelahiran_id'],
            ]);

            $additionals['pasien'] = $pasienPayLoad->attributes;
            $additionals['pasien']['golonganumur_id'] = $getGolUmurId;
        }

        $eligiblePasien = $request->post('eligible_pasien', false);
        $eligiblePasien = json_decode($eligiblePasien, true);
        $dataPeserta = ArrayHelper::getValue($eligiblePasien, 'dataPeserta', []);

        $dataPendaftaran = [
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienRecord['pasien_id'],
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => DocoConstants::INST_ID_RI,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
            'golonganumur_id' => $getGolUmurId,
            'umur' => ucwords(DocoHelpers::getUmur($pasienRecord['tanggal_lahir'])),
            'rujukan_id' => null,
            'antrian_id' => null,
            'kunjungan' => DocoConstants::VAR_K_L,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => null,
            'keadaan_masuk' => null,
            'status_periksa' => DocoConstants::VAR_SP_BP,
            'status_pasien' => DocoConstants::VAR_PAS_L,
            'status_masuk' => DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadPasienAdmisi->keterangan,
            'is_karcis' => true,
            'additional_data' => json_encode($additionals),
            'is_aps' => false,
            'limit_tagihan' => $limitTagihan,
            'petugas_id' => IdentifyUser::getIdentity()->user->loginpemakai_id,
            'petugas_tgl_pembuat' => date('Y-m-d H:i:s'),
            'prev_pendaftaran_id' => $pendaftaranAsalId,
            'is_multipayer' => $isMultiPayer,
            'referal_pegawai_id' => (int) $payloadKunjungan->referal > 0 ? $payloadKunjungan->referal : null,
            'referal_luar' => (int) $payloadKunjungan->referal == 0 ? strtoupper($payloadKunjungan->referal) : null,
            'eligible_pasien' => $eligiblePasien,
            'data_peserta' => $dataPeserta,
            'referensi_asuransi' => $request->post('refresh_asuransi')
        ];

        return $this->save($dataPendaftaran, $additionals, $payload, $payloadBpjs, $isBpjs, $allowBpjs, $idKelasPelayanan, $idRuangan, $isBbl, $konfig, $latestMedicalRecord, $detailTindakan);
    }

    public function actionSavePendaftaranRanap()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $isBpjs = false;
        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $listTagihan = [];
        /** allow Bpjs */
        $allowBpjs = $request->post('allow_bpjs',false);
        /**
         * @var app\modules\v1\payload\PjPasien
         */
        $pjPasien = [];

        /**
         * @var app\modules\v1\payload\BpjsNewForm
         */
        $bpjs = [];

        /**
         * @var app\modules\v1\payload\PayloadRujukan
         */
        $rujukan = [];

        /**
         * @var app\modules\v1\payload\AsuransiForm
         */
        $asuransi = [];

        /**
        * @var app\modules\v1\payload\KeluargaPasienForm
        */
        $keluargapasien = [];
        $keluargapasienOld = [];

        /**
         * @var app\modules\v1\payload\PenanggungBiayaForm
         */
        $penanggungbiaya = [];

        $dokterkonsul = [];

        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $payloadPasienAdmisi = new PasienAdmisiPayload;
        $pasienPayLoad = new PayloadPasien;
        $pasienPayLoad->scenario = "pendaftaran-ranap";
        $payloadPasienAdmisi->scenario = "pendaftaran-styp";
        $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
        $payloadPasienAdmisi->attributes = $post['pasien_admisi'];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payloadKunjungan->is_ranap = true;
        $payloadPasienAdmisi->keterangan = $payloadPasienAdmisi->keterangan;

        /* Default Jenis Kasus Penyakit Umum */
        $payloadKunjungan->jeniskasuspenyakit_id = 23;
        $payloadKunjungan->kelaspelayanan_id = $post['kelaspelayanan_selected'];
        if (!empty($post['pasien_admisi']['dokterkonsul_id'])) {
            foreach ($post['pasien_admisi']['dokterkonsul_id'] as $key => $value) {
                if(!empty($value)) {
                    $dokterkonsul[] = $value;
                } else {
                    break;
                }
            }
        }

        if (!empty($post['bpjs'])) {
            $payloadBpjs = new BpjsNewForm;
            $payloadBpjs->attributes = !empty($post['bpjs']) ? $post['bpjs'] : null;
            $payload->no_rekam_medik = $payloadBpjs->no_rekam_medik;
            $pasienPayLoad->nopeserta_bpjs = !empty($payloadBpjs->no_kartu) ? $payloadBpjs->no_kartu : null;
            $isBpjs = true;
            /** Statis Rujukan WIP */
            $diagnosa = Diagnosa::find()->select([
                'diagnosa_id'
            ])->where([
                'diagnosa_kode' => $payloadBpjs->diagnosa_awal
            ])->one();
            $payload->asalrujukan_id = 33;
            $post['rujukan'] = [
                'rujukandari_id' => 1,
                'no_rujukan' => $payloadBpjs->no_rujukan ? $payloadBpjs->no_rujukan : $payloadBpjs->no_kartu,
                'nama_perujuk' => 'BPJS',
                'tanggal_rujukan' => date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan)),
                'kodediagnosa_rujukan' => $payloadBpjs->diagnosa_awal,
                'diagnosa_id' => !empty($diagnosa->diagnosa_id) ? $diagnosa->diagnosa_id : null,
            ];
             //** Override Value Penjamin */
            $payload->penjamin_id = !empty($payloadBpjs->jenis_peserta) ? $payloadBpjs->jenis_peserta : $payload->penjamin_id;
            if (!$payloadBpjs->validate()) $errorParse['bpjs'] = $payloadBpjs->errors;
        }

        if (!empty($payload->no_rekam_medik) && !isset($post['pasien']['nama_pasien'])) {
            $pasien = Pasien::find()->where([
                'no_rekam_medik' => $payload->no_rekam_medik
            ])->asArray()->one();
            if (empty($pasien)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien tidak ditemukan'
                ];
            }
            $pasienPayLoad->attributes = $pasien;
            $pasienBaru = false;
            $keluargapasienOld = KeluargaPasien::find()->where([
                'pasien_id' => $pasienPayLoad->pasien_id
            ])->one();
        }
        foreach ($pasienPayLoad->attributes as $key => $value) {
            if (empty($pasienPayLoad->attributes[$key])) {
                $pasienPayLoad->{$key} = null;
            }
        }

        if (!empty($post['penanggungbiaya'])) {
            $payloadPenanggungBiaya = new PenanggungBiayaForm;
            $payloadPenanggungBiaya->attributes = $post['penanggungbiaya'];
            $penanggungbiaya = $payloadPenanggungBiaya->attributes;
            if (!$payloadPenanggungBiaya->validate()) $errorParse['penanggungbiaya'] = $payloadPenanggungBiaya->errors;
        }

        /** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir)
                            ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;
        $pasienPayLoad->jenisidentitas = !empty($pasienPayLoad->jenisidentitas) ? $pasienPayLoad->jenisidentitas : null;
        $pasienPayLoad->statusperkawinan = !empty($pasienPayLoad->statusperkawinan) ? $pasienPayLoad->statusperkawinan : null;
        $pasienPayLoad->namadepan = !empty($pasienPayLoad->namadepan) ? $pasienPayLoad->namadepan : null;
        $pasienPayLoad->catatanpenting_pasien =  !empty($payloadKunjungan->catatanpenting_pasien) ? $payloadKunjungan->catatanpenting_pasien : null;
        if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;
        if (!empty($post['keluarga_pasien'])) {
            $payloadKp = new KeluargaPasienForm;
            $payloadKp->attributes = $post['keluarga_pasien'];
            foreach ($payloadKp->attributes as $key => $value) {
                if (empty($payloadKp->attributes[$key])) {
                    $payloadKp->{$key} = null;
                }
            }
            $keluargapasien = !empty($payloadKp->attributes) ? $payloadKp->attributes : [] ;
            if (!$payloadKp->validate()) $errorParse['keluarga_pasien'] = $payloadKp->errors;
        }

        if (!empty($post['pj_pasien'])) {
            $payloadPjPasien = new PjPasien;
            $payloadPjPasien->attributes = $post['pj_pasien'];
            $payloadPjPasien->pj_tanggal_lahir = !empty($payloadPjPasien->pj_tanggal_lahir)
                                        ? date('Y-m-d',strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            if ($payloadPjPasien->pj_pengantar == 990){
                $payloadPjPasien->pj_nama = !empty($pasienPayLoad->nama_pasien) ? $pasienPayLoad->nama_pasien : null;
                $payloadPjPasien->pj_jk = !empty($pasienPayLoad->jeniskelamin) ? $pasienPayLoad->jeniskelamin : null;
                $payloadPjPasien->pj_jenis_identitas = !empty($pasienPayLoad->jenisidentitas) ? $pasienPayLoad->jenisidentitas : null;
                $payloadPjPasien->pj_no_identitas = !empty($pasienPayLoad->no_identitas_pasien) ? $pasienPayLoad->no_identitas_pasien : null;
                $payloadPjPasien->pj_tempat_lahir = !empty($pasienPayLoad->tempat_lahir) ? $pasienPayLoad->tempat_lahir : null;
                $payloadPjPasien->pj_tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir) ? $pasienPayLoad->tanggal_lahir : null;
                $payloadPjPasien->pj_umur = !empty($pasienPayLoad->umur) ? $pasienPayLoad->umur : null;
                $payloadPjPasien->pj_alamat = !empty($pasienPayLoad->alamat_pasien) ? $pasienPayLoad->alamat_pasien : null;
                $payloadPjPasien->pj_no_telepon = !empty($pasienPayLoad->no_telepon_pasien) ? $pasienPayLoad->no_telepon_pasien : null;
                $payloadPjPasien->pj_namadepan = !empty($pasienPayLoad->namadepan) ? $pasienPayLoad->namadepan : null;
                $payloadPjPasien->pj_propinsi_id = !empty($pasienPayLoad->propinsi_id) ? $pasienPayLoad->propinsi_id : null;
                $payloadPjPasien->pj_kabupaten_id = !empty($pasienPayLoad->kabupaten_id) ? $pasienPayLoad->kabupaten_id : null;
                $payloadPjPasien->pj_kecamatan_id = !empty($pasienPayLoad->kecamatan_id) ? $pasienPayLoad->kecamatan_id : null;
                $payloadPjPasien->pj_kelurahan_id = !empty($pasienPayLoad->kelurahan_id) ? $pasienPayLoad->kelurahan_id : null;
                $payloadPjPasien->pj_pekerjaan_id = !empty($pasienPayLoad->pekerjaan_id) ? $pasienPayLoad->pekerjaan_id : null;
                $payloadPjPasien->pj_rt = !empty($pasienPayLoad->rt) ? $pasienPayLoad->rt : null;
                $payloadPjPasien->pj_rw = !empty($pasienPayLoad->rw) ? $pasienPayLoad->rw : null;
            } else if ($payloadPjPasien->pj_pengantar == 991) {
                if (!empty($post['keluarga_pasien'])){
                    $payloadPjPasien->pj_nama = !empty($payloadKp->keluarga_nama) ? $payloadKp->keluarga_nama : null;
                    $payloadPjPasien->pj_jk = !empty($payloadKp->keluarga_jk) ? $payloadKp->keluarga_jk : null;
                    $payloadPjPasien->pj_hubungan = !empty($payloadKp->keluarga_hubungan) ? $payloadKp->keluarga_hubungan : null;
                    $payloadPjPasien->pj_alamat = !empty($payloadKp->keluarga_alamat) ? $payloadKp->keluarga_alamat : null;
                    $payloadPjPasien->pj_no_telepon = !empty($payloadKp->keluarga_no_telepon) ? $payloadKp->keluarga_no_telepon : null;
                    $payloadPjPasien->pj_namadepan = !empty($payloadKp->keluarga_namadepan) ? $payloadKp->keluarga_namadepan : null;
                    $payloadPjPasien->pj_propinsi_id = !empty($payloadKp->keluarga_propinsi_id) ? $payloadKp->keluarga_propinsi_id : null;
                    $payloadPjPasien->pj_kabupaten_id = !empty($payloadKp->keluarga_kabupaten_id) ? $payloadKp->keluarga_kabupaten_id : null;
                    $payloadPjPasien->pj_kecamatan_id = !empty($payloadKp->keluarga_kecamatan_id) ? $payloadKp->keluarga_kecamatan_id : null;
                    $payloadPjPasien->pj_kelurahan_id = !empty($payloadKp->keluarga_kelurahan_id) ? $payloadKp->keluarga_kelurahan_id : null;
                    $payloadPjPasien->pj_pekerjaan_id = !empty($payloadKp->keluarga_pekerjaan_id) ? $payloadKp->keluarga_pekerjaan_id : null;
                    $payloadPjPasien->pj_rt = !empty($payloadKp->keluarga_rt) ? $payloadKp->keluarga_rt : null;
                    $payloadPjPasien->pj_rw = !empty($payloadKp->keluarga_rw) ? $payloadKp->keluarga_rw : null;
                } else if (!empty($keluargapasienOld)) {
                    $payloadPjPasien->pj_nama = !empty($keluargapasienOld->keluarga_nama) ? $keluargapasienOld->keluarga_nama : null;
                    $payloadPjPasien->pj_jk = !empty($keluargapasienOld->keluarga_jk) ? $keluargapasienOld->keluarga_jk : null;
                    $payloadPjPasien->pj_hubungan = !empty($keluargapasienOld->keluarga_hubungan) ? $keluargapasienOld->keluarga_hubungan : null;
                    $payloadPjPasien->pj_alamat = !empty($keluargapasienOld->keluarga_alamat) ? $keluargapasienOld->keluarga_alamat : null;
                    $payloadPjPasien->pj_no_telepon = !empty($keluargapasienOld->keluarga_no_telepon) ? $keluargapasienOld->keluarga_no_telepon : null;
                    $payloadPjPasien->pj_namadepan = !empty($keluargapasienOld->keluarga_namadepan) ? $keluargapasienOld->keluarga_namadepan : null;
                    $payloadPjPasien->pj_propinsi_id = !empty($keluargapasienOld->keluarga_propinsi_id) ? $keluargapasienOld->keluarga_propinsi_id : null;
                    $payloadPjPasien->pj_kabupaten_id = !empty($keluargapasienOld->keluarga_kabupaten_id) ? $keluargapasienOld->keluarga_kabupaten_id : null;
                    $payloadPjPasien->pj_kecamatan_id = !empty($keluargapasienOld->keluarga_kecamatan_id) ? $keluargapasienOld->keluarga_kecamatan_id : null;
                    $payloadPjPasien->pj_kelurahan_id = !empty($keluargapasienOld->keluarga_kelurahan_id) ? $keluargapasienOld->keluarga_kelurahan_id : null;
                    $payloadPjPasien->pj_pekerjaan_id = !empty($keluargapasienOld->keluarga_pekerjaan_id) ? $keluargapasienOld->keluarga_pekerjaan_id : null;
                    $payloadPjPasien->pj_rt = !empty($keluargapasienOld->keluarga_rt) ? $keluargapasienOld->keluarga_rt : null;
                    $payloadPjPasien->pj_rw = !empty($keluargapasienOld->keluarga_rw) ? $keluargapasienOld->keluarga_rw : null;
                }
            }
            $pjPasien = !empty($payloadPjPasien->attributes) ? $payloadPjPasien->attributes : null;
            if (!$payloadPjPasien->validate()) $errorParse['pj_pasien'] = $payloadPjPasien->errors;
        }
        // condition if pendaftaran bbl it will create new pasien record
        $isBbl = false;
        if (isset($post['is_bbl'])) {
            $isBbl = true;
            $pasienPayLoad = new PayloadPasien;
            $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
            $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
            $pasienPayLoad->is_aps = false;
            // 100 jenis identitas lainnya
            $pasienPayLoad->jenisidentitas = '100';
            $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir) ? date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;
            if (!$pasienPayLoad->validate()) {
                $errorParse['pasien'] = $pasienPayLoad->errors;
            }

        }
        /** Validate Form */
        if (!$payload->validate()) {
            $errorParse['tipe_pasien'] = $payload->errors;
        }

        if (!$payloadKunjungan->validate()) {
            $errorParse['kunjungan'] = $payloadKunjungan->errors;
        }

        if (!$payloadPasienAdmisi->validate()) {
            $errorParse['pasien_admisi'] = $payloadPasienAdmisi->errors;
        }

        // Set asuransi
        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->carabayar_id = !empty($payload->carabayar_id) ? $payload->carabayar_id : null;
            $payloadAsuransi->penjamin_id = !empty($payload->penjamin_id) ? $payload->penjamin_id : null;
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? $payloadAsuransi->tgl_konfirmasi : null;
            $payloadAsuransi->masaberlakukartu = !empty($payloadAsuransi->masaberlakukartu) ? date('Y-m-d', strtotime($payloadAsuransi->masaberlakukartu)) : null;
            $asuransi = $payloadAsuransi->attributes;
            if (!$payloadAsuransi->validate()) $errorParse['asuransi'] = $payloadAsuransi->errors;
        }

        if (!empty($post['bpjs'])) {
            $payloadBpjs = new BpjsNewForm;
            $payloadBpjs->attributes = !empty($post['bpjs']) ? $post['bpjs'] : null;
            $payloadBpjs->no_rekam_medik = !empty($payload->no_rekam_medik) ? $payload->no_rekam_medik : null;
            $pasienPayLoad->nopeserta_bpjs = !empty($payloadBpjs->no_kartu) ? $payloadBpjs->no_kartu : null;

            /** Determine patient is upgrade class or not */
            $kp = KelasPelayanan::find()
            ->select(['bpjs_kelas','kelas_rawat_naik_bpjs'])
            ->where([
                'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id
            ])
            ->one();

           $kelasNaik = ArrayHelper::getValue($kp, 'kelas_rawat_naik_bpjs');
           $kp = ArrayHelper::getValue($kp, 'bpjs_kelas');
        //    $infoBpjs = json_decode($payloadBpjs->info_response, true);
        //    $hakKelas = ArrayHelper::getValue($infoBpjs, 'peserta.hakKelas.kode');
           $hakKelas = (int) $payloadBpjs->kelas_rawat;
           $listKelasBpjs = $this->lookup_type->actionGetLookupType('kelas_rawat_naik_bpjs');
           $isNaikLebihDari2Kelas = false;

           /** @todo
            * case ICU/ICCU belum ada penjelasan
            * $kp 1,2,3 masuk kondisi ini
            */
            if(!empty($kp) && !empty($kelasNaik)) {
                if((int) $kp < (int) $payloadBpjs->kelas_rawat) {
                    if(!empty($hakKelas)) {
                        if($kp < (int) $hakKelas) {
                            /** case hak kelas 3 ke kelas 1 */
                            if($hakKelas == BpjsConstans::KELAS_3 && $kp == BpjsConstans::KELAS_1) {
                                $isNaikLebihDari2Kelas = true;
                            }
                        }
                    }

                    if(!$isNaikLebihDari2Kelas) {
                        if(!empty($listKelasBpjs)) {
                            if(!empty($kelasNaik)) {
                                foreach($listKelasBpjs as $val) {
                                    if($val['lookup_id'] == $kelasNaik) {
                                        $mappedKelasNaik = $val['lookup_value'];
                                        break;
                                    }
                                }
                            }
                        }

                        if(!isset($mappedKelasNaik)) {
                            return [
                                'status' => 422,
                                'title' => 'Proses Gagal!',
                                'text' => 'Kelas yang dipilih belum di mapping dengan kelas naik bpjs!.',
                            ];
                        }
                        $payloadBpjs->klsRawatNaik = (int) $mappedKelasNaik;
                        $payloadBpjs->pembiayaan = "1"; //@todo masih hardcode
                        $payloadBpjs->penanggungJawab = "Pribadi"; //@todo masih hardcode
                    } else {
                        $payloadBpjs->klsRawatNaik = null;
                        $payloadBpjs->pembiayaan = "";
                        $payloadBpjs->penanggungJawab = "";
                    }
                }
            } else if (is_null($kp) && !empty($hakKelas)) {
                $mappedKelasHak = null;
                $mappedKelasPelayanan = null;
                if(!empty($listKelasBpjs)) {
                    foreach($listKelasBpjs as $val) {
                        if($val['lookup_kode'] == $hakKelas) {
                            $mappedKelasHak = $val['lookup_value'];
                        }
                        if($val['lookup_kode'] == $kp) {
                            $mappedKelasPelayanan = $val['lookup_value'];
                        }
                    }
                }

                if(empty($mappedKelasHak) || empty($mappedKelasPelayanan)) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Kelas bpjs belum dimapping!',
                    ];
                }

                if($mappedKelasHak != $mappedKelasPelayanan) {
                    /** case mapped hak kelas 1 ke vvip */
                    if($mappedKelasHak == BpjsConstans::V_NAIK_KELAS_1 && $mappedKelasPelayanan == BpjsConstans::V_NAIK_KELAS_VVIP) {
                        $isNaikLebihDari2Kelas = true;
                    } else if ($mappedKelasHak == BpjsConstans::V_NAIK_KELAS_3 && $mappedKelasPelayanan != BpjsConstans::V_NAIK_KELAS_2) { // hak kelas 3 jika tidak ke kelas 2 dianggap naik2kelas (iccu sama icu dianggap naik 2 kelas)
                        $isNaikLebihDari2Kelas = true;
                    } else if ($mappedKelasHak == BpjsConstans::V_NAIK_KELAS_2 && ($mappedKelasPelayanan != BpjsConstans::V_NAIK_KELAS_1 || $mappedKelasPelayanan != BpjsConstans::V_NAIK_KELAS_3)) { // hak kelas 2 jika tidak ke kelas 1 atau kelas 3 dianggap naik2kelas (iccu sama icu dianggap naik 2 kelas)
                        $isNaikLebihDari2Kelas = true;
                    }

                    if($isNaikLebihDari2Kelas) {
                        $payloadBpjs->klsRawatNaik = null;
                        $payloadBpjs->pembiayaan = "";
                        $payloadBpjs->penanggungJawab = "";
                    }
                }
            }

            $isBpjs = true;
            /** Statis Rujukan WIP */
            $diagnosa = Diagnosa::find()->select([
                'diagnosa_id'
            ])->where([
                'diagnosa_kode' => $payloadBpjs->diagnosa_awal
            ])->one();
            $payload->asalrujukan_id = 33;
            $post['rujukan'] = [
                'rujukandari_id' => 1,
                'no_rujukan' => $payloadBpjs->no_rujukan ? $payloadBpjs->no_rujukan : $payloadBpjs->no_kartu,
                'nama_perujuk' => 'BPJS',
                'tanggal_rujukan' => date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan)),
                'kodediagnosa_rujukan' => $payloadBpjs->diagnosa_awal,
                'diagnosa_id' => !empty($diagnosa->diagnosa_id) ? $diagnosa->diagnosa_id : null,
            ];
            $payload->penjamin_id = $payloadBpjs->jenis_peserta;
            if (!$payloadBpjs->validate()) $errorParse['bpjs'] = $payloadBpjs->errors;
        }

        /** Set Rujukan */
        if (!empty($post['rujukan'])) {
            $payloadRujukan = new PayloadRujukan;
            $payloadRujukan->attributes = $post['rujukan'];
            $payloadRujukan->asalrujukan_id = !empty($payload->asalrujukan_id) ? $payload->asalrujukan_id : null;
            $payloadRujukan->tanggal_rujukan = date('Y-m-d', strtotime($payloadRujukan->tanggal_rujukan));
            $rujukan = !empty($payloadRujukan->attributes) ? $payloadRujukan : null;
            if (!$payloadRujukan->validate()) $errorParse['rujukan'] = $payloadRujukan->errors;
        }

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse,
            ];
        }

        if ($payloadPasienAdmisi->is_pasientitipan) {
            $model = (new TarifTotalRsFn([
                'extParam' => [
                    $payloadPasienAdmisi->ruangan_titipan_id,
                    $payload->penjamin_id,
                    $payloadPasienAdmisi->kelas_ditagihkan_id,
                    'kamar'
                ]
            ]));
            $ruanganAkomodasiRecord = $model::find()
            ->andWhere([
                'kamarruangan_id' => $payloadPasienAdmisi->kamar_titipan_id
            ])
            ->one();
        } else {
            $model = (new TarifTotalRsFn([
                'extParam' => [
                    $payloadKunjungan->ruangan_id,
                    $payload->penjamin_id,
                    $post['kelaspelayanan_selected'],
                    'kamar'
                ]
            ]));
            $ruanganAkomodasiRecord = $model::find()
            ->andWhere([
                'kamarruangan_id' => $payloadPasienAdmisi->kamarruangan_id
            ])
            ->one();
        }

        $pendaftaranAsalId = null;
        if (!$isBbl) {
            // get pasien record by no rekam medik

            if(!empty($payload->no_rekam_medik)){

                $pasienRecord = Pasien::find()->where([
                    'no_rekam_medik' => $payload->no_rekam_medik,
                ])->select(['pasien_id', 'tanggal_lahir'])->asArray()->one();
                if (empty($pasienRecord)) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Pasien tidak ditemukan',
                    ];
                }

                // Checking existing active rd or rj record
                $latestMedicalRecord = PasienPulangRdRjView::find()
                    ->andWhere([
                        'pasien_id' => $pasienRecord['pasien_id'],
                    ])
                    ->select([
                        'pendaftaran_id',
                        'is_ranap',
                        'pasienadmisi_id',
                        'instalasi_id',
                    ])
                    ->orderBy('pasienpulangrdrj_v.tgl_pendaftaran DESC')
                    ->asArray()
                    ->one();
                $pendaftaranAsalId = !empty($latestMedicalRecord) ? $latestMedicalRecord['pendaftaran_id'] : null;
            }else {

                $pasienRecord = [
                    'pasien_id' => null,
                    'tanggal_lahir' => !empty($pasienPayLoad->tanggal_lahir) ? $pasienPayLoad->tanggal_lahir : null,
                ];
            }

        } else {
            $pasienRecord = [
                'pasien_id' => null,
                'tanggal_lahir' => !empty($pasienPayLoad->tanggal_lahir) ? $pasienPayLoad->tanggal_lahir : null,
            ];
        }

        // Mapping golongan
        $getTotalhari = DocoHelpers::convertToHari($pasienRecord['tanggal_lahir']);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;
        foreach ($getGolongan as $value) {
            if ($value['golonganumur_minimal'] <= $getTotalhari
                && $value['golonganumur_maksimal'] >= $getTotalhari) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }

        $dataCaraBayar = CaraBayar::findOne($payload->carabayar_id);
        $groupCaraBayarId = ArrayHelper::getValue($dataCaraBayar, 'groupcarabayar_id');
        $limitTagihan = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;
        if ($isBpjs || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon(DocoConstants::INST_ID_RI, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }

        $additionals = [
            'tarif' => $listTagihan,
            'penanggung_jawab' => $pjPasien,
            'pasien_admisi' => [
                'ruangan_id' => !empty($payloadKunjungan->ruangan_id) ? $payloadKunjungan->ruangan_id : null,
                'pasien_id' => !empty($pasienRecord['pasien_id']) ? $pasienRecord['pasien_id']: null ,
                'kamarruangan_id' => !empty($payloadPasienAdmisi->kamarruangan_id) ? $payloadPasienAdmisi->kamarruangan_id: null,
                'kamartempattidur_id' => !empty($payloadPasienAdmisi->kamartempattidur_id) ? $payloadPasienAdmisi->kamartempattidur_id : null,
                'kelaspelayanan_id' => !empty($post['kelaspelayanan_selected']) ? $post['kelaspelayanan_selected'] : $payloadKunjungan->kelaspelayanan_id,
                'pegawai_id' => !empty($payloadPasienAdmisi->pegawai_id) ? $payloadPasienAdmisi->pegawai_id : null ,
                'tgl_admisi' => date("Y-m-d H:i:s", strtotime($payloadPasienAdmisi->tgl_admisi)),
                'tgl_pendaftaran' => date("Y-m-d H:i:s"),
                'kunjungan' => DocoConstants::VAR_K_L,
                'bpjs_id' => null,
                'status_ranap' => DocoConstants::STATUS_RANAP_BELUM_PERIKSA,
                'is_pasientitipan' => ($payloadPasienAdmisi->is_pasientitipan == 1) ? true : false,
                'is_aps' => ($payloadPasienAdmisi->is_aps == 1) ? true : false,
                'carabayar_id' => $payload->carabayar_id ? (int) $payload->carabayar_id : null,
                'penjamin_id' => $payload->penjamin_id ? (int) $payload->penjamin_id : null,
                'kamar_titipan_id' => $payloadPasienAdmisi->kamar_titipan_id ? (int)$payloadPasienAdmisi->kamar_titipan_id : null,
                'kelas_ditagihkan_id' => $payloadPasienAdmisi->kelas_ditagihkan_id ? (int)$payloadPasienAdmisi->kelas_ditagihkan_id : null,
                'ruangan_titipan_id' => $payloadPasienAdmisi->ruangan_titipan_id ? (int)$payloadPasienAdmisi->ruangan_titipan_id : null,
                'limit_tagihan' => $limitTagihan,
                'dokterpengirim_id' => $payloadPasienAdmisi->dokterpengirim_id,
                'hakkelas_id' => $payloadPasienAdmisi->hakkelas_id ? (int) $payloadPasienAdmisi->hakkelas_id : null,
                'kelaspermintaan_id' => $payloadPasienAdmisi->kelaspermintaan_id ? (int)$payloadPasienAdmisi->kelaspermintaan_id : null,
                'dokterkonsul_id' => empty($dokterkonsul) ? null : json_encode($dokterkonsul),
                'prosedurmasuk_id' => $payloadPasienAdmisi->prosedurmasuk_id ? $payloadPasienAdmisi->prosedurmasuk_id : null,
                'diagnosa_awal' => $payloadPasienAdmisi->diagnosa_awal ? $payloadPasienAdmisi->diagnosa_awal : null
            ],
            'masuk_kamar' => [
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'pegawai_id' => $payloadPasienAdmisi->pegawai_id,
                'kelaspelayanan_id' =>  !empty($post['kelaspelayanan_selected']) ? $post['kelaspelayanan_selected'] : $payloadKunjungan->kelaspelayanan_id,
                'kamartempattidur_id' => $payloadPasienAdmisi->kamartempattidur_id,
                'kamarruangan_id' => $payloadPasienAdmisi->kamarruangan_id,
                'tgl_masukkamar' => date("Y-m-d", strtotime($payloadPasienAdmisi->tgl_admisi)),
                'jam_masukkamar' => date("H:i:s", strtotime($payloadPasienAdmisi->tgl_admisi)),
            ],
            'asuransi' => $asuransi,
            'pendaftaranasal_id' => $pendaftaranAsalId,
            'rujukan' => $rujukan,
            'penanggungbiaya' => $penanggungbiaya,
            'keluargapasien' => $keluargapasien,
            'antrian' => [],
        ];
        if ($isBbl) {
            $additionals = array_merge($additionals, [
                'pendaftaran_ibu_id' => $post['pendaftaran_ibu_id'],
                'kelahiran_id' => $post['kelahiran_id'],
            ]);

            $additionals['pasien'] = $pasienPayLoad->attributes;
            $additionals['pasien']['golonganumur_id'] = !empty($getGolUmurId) ? $getGolUmurId : null;
        }
        if (empty($payload->no_rekam_medik)) {
            // $additionals = array_merge($additionals, [
            //     'pendaftaran_ibu_id' => $post['pendaftaran_ibu_id'],
            //     'kelahiran_id' => $post['kelahiran_id'],
            $additionals['pasien'] = $pasienPayLoad->attributes;
            $additionals['pasien']['golonganumur_id'] = !empty($getGolUmurId) ? $getGolUmurId : null;
        }
        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        $dataPendaftaran = [
            'caramasuk_id' => $payload->asalrujukan_id,
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienRecord['pasien_id'],
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => DocoConstants::INST_ID_RI,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => !empty($post['kelaspelayanan_selected']) ? $post['kelaspelayanan_selected'] : $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
            'golonganumur_id' => $getGolUmurId,
            'umur' => ucwords(DocoHelpers::getUmur($pasienRecord['tanggal_lahir'])),
            'rujukan_id' => null,
            'antrian_id' => null,
            'kunjungan' => DocoConstants::VAR_K_L,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => null,
            'keadaan_masuk' => null,
            'status_periksa' => DocoConstants::VAR_SP_BP,
            'status_pasien' => $statusPasien,
            'status_masuk' => DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadPasienAdmisi->keterangan,
            'is_karcis' => true,
            'additional_data' => json_encode($additionals),
            'is_aps' => false,
            'limit_tagihan' => $limitTagihan,
        ];


        $pendaftaranId = '';
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran = new Pendaftaran();
            $pendaftaran->attributes = $dataPendaftaran;
            $tmpPendaftaran = (new Penomoran)->setNoReg(DocoConstants::INST_ID_RI);

            if(empty($tmpPendaftaran)) return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'No pendaftaran tidak tersedia.'
            ];

            $getLastRm = (new Penomoran)->getSetNoRm(null, self::PENOMORAN_PASIEN);
            if(empty($getLastRm)) return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Gagal mendapatkan No Rekam Medik.'
            ];

            $prosesBpjs = false;
            $pendaftaran->no_pendaftaran = $tmpPendaftaran['no_reg'];

            if ($pendaftaran->validate() && $pendaftaran->save(false)) {
                $pendaftaranId = $pendaftaran->pendaftaran_id;
                if ($isBpjs && !$allowBpjs) {
                    $prosesBpjs = true;
                    $bpjsRes = $this->saveBpjs($payloadBpjs, $pendaftaranId, $isBbl, true);
                    if (is_array($bpjsRes)) return $bpjsRes;
                }  else if ($isBpjs && $allowBpjs) { //handle bpjs unauth
                    $bpjsRes = $this->saveBpjsWithouBridging($payloadBpjs, $pendaftaranId, $isBbl, true);
                    $prosesBpjs = false;
                    // sleep(20);
                }

                $transaction->commit();

                $params['route'] = 'app/save-pendaftaran-ranap';
                $params['data'] = $this->syncPendaftaran($pendaftaranId);
                $sendData = (new PendaftaranService)->syncPendaftaranSty($params, function($data, $result) {
                    return $result;
                });
                $payload = $params['data'];
                $this->setLog($pendaftaranId, $payload, $sendData);
                (new Penomoran)->save($tmpPendaftaran['konfig_id'], 1, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
                (new Penomoran)->getSetNoRm(null, self::PENOMORAN_PASIEN, 'save');

                return [
                    'message' => 'Proses Pendaftaran berhasil',
                    'id' => DocoHelpers::encrypt($pendaftaranId),
                    'is_bpjs' => $prosesBpjs
                ];

            }
            return [
                'status' => 422,
                'data' => $pendaftaran->errors
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/', $e->getMessage(), $out);
            (new Penomoran)->save($tmpPendaftaran['konfig_id'], 0, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = 'Query :' . $out[1];
            }
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            (new Penomoran)->save($tmpPendaftaran['konfig_id'], 0, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage(),
            ];
        }
    }

    /**
     * This function will get data pendaftaran
     * and send it to serconn for
     * Sync Santo Yusup
     *
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    private function syncPendaftaran($idPendaftaran)
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = [];
        $pdftrn = SyPendaftaranView::find()
        ->where(['pendaftaran_id' => $idPendaftaran])
        ->asArray()
        ->one();

        if(!empty($pdftrn)) {
            $add = json_decode($pdftrn['additional_data'], true);

            if(!empty($pdftrn['asuransipasien_id'])) {
                $asuransi = SyAsuransiPasienView::find()
                ->where(['asuransipasien_id' => $pdftrn['asuransipasien_id']])
                ->asArray()
                ->one();
            }

            //** keluarga harus selalu di kirim */
            // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                $keluarga = SyKeluargaPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['keluargapasien_id' => SORT_DESC])
                ->asArray()
                ->one();
            // }

            if(isset($add['penanggungbiaya']) && !empty($add['penanggungbiaya'])) {
                $penanggung = SyPenanggungBiayaView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungbiaya_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            if(isset($add['penanggung_jawab']) && !empty($add['penanggung_jawab'])) {
                $penanggungJawab = SyPenanggungJawabView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungjawab_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            $pasien = SyPasienView::find()
            ->where(['pasien_id' => $pdftrn['pasien_id']])
            ->asArray()
            ->one();

            if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                $umurExp = explode(" ",$pdftrn['umur']);
                $pdftrn['umur_hari'] = $umurExp[4];
                $pdftrn['umur_bulan'] = $umurExp[2];
                $pdftrn['umur_tahun'] = $umurExp[0];
            }

            if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                $additionalPasien = json_decode($pasien['additional_pasien']);
                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                    if(isset($ktp)) {
                        $pasien['nik'] = $ktp;
                    }
                }
            }

            if (isset($pdftrn['dokterkonsul']) && !empty($pdftrn['dokterkonsul'])) {
                $listDokter = json_decode($pdftrn['dokterkonsul']);
                $pdftrn['dok_konsul'] = [];
                $getDokter = Pegawai::find()
                            ->select('additional_data')
                            ->where(['IN', 'pegawai_id', $listDokter])
                            ->asArray()
                            ->all();
                for ($i=0; $i < count($getDokter); $i++) {
                    $pdftrn['dok_konsul'][] = $getDokter[$i]['additional_data'];
                }
            }

            $penjamin = SyPenjaminView::find()
            ->where(['penjamin_id' => $pdftrn['penjamin_id']])
            ->asArray()
            ->one();
        }

        $result = [
            'pendaftaran' => $pdftrn,
            'pasien' => $pasien,
            'asuransi' => $asuransi,
            'keluarga' => $keluarga,
            'penanggung' => $penanggung,
            'penjamin' => $penjamin,
            'penanggungJawab' => $penanggungJawab,
        ];

        // $modelSync = new SyncsantoyusupR;
        // $modelSync->pendaftaran_id = $pdftrn['pendaftaran_id'];
        // $modelSync->pasien_id = $pdftrn['pasien_id'];
        // $modelSync->created_date = date("Y-m-d H:i:s");
        // $modelSync->count_sync = 1;
        // $modelSync->payload = json_encode($result);
        // $modelSync->save(false);

        return $result;
    }

    private function setLog($idPendaftaran, $result, $sendData)
    {
        $modelSync = new SyncsantoyusupR;
        $modelSync->pendaftaran_id = $idPendaftaran;
        $modelSync->pasien_id = null;
        $modelSync->created_date = date("Y-m-d H:i:s");
        $modelSync->count_sync = 1;
        $modelSync->payload = json_encode($result);
        $modelSync->uid = json_encode($sendData);
        $modelSync->save(false);
    }

    private function save($dataPendaftaran , $additionals, $payload, $payloadBpjs, $isBpjs , $allowBpjs, $idKelasPelayanan, $idRuangan, $isBbl, $konfig, $latestMedicalRecord, $detailTindakan)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $petugas_id = $jwt->loginpemakai_id;
        $petugas_tgl_pembuat = date('Y-m-d H:i:s');
        $pendaftaranId = '';
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            // DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD
            // condition if last medical record is rawat darurat it will update additional data related record rawat darurat else it will create new record pendaftaran
            if(isset($konfig['is_set_igdkeri']) && $konfig['is_set_igdkeri'] != true) {
                if (isset($latestMedicalRecord) && $latestMedicalRecord['instalasi_id'] === DocoConstants::INST_ID_RD) {
                    $pendaftaranId = $latestMedicalRecord['pendaftaran_id'];
                    $pendaftaran = Pendaftaran::findOne($pendaftaranId);
                    $pendaftaran->additional_data = json_encode($additionals);
                    $pendaftaran->last_modified_by = IdentifyUser::getIdentity()->user->loginpemakai_id;
                    $pendaftaran->petugas_id = IdentifyUser::getIdentity()->user->loginpemakai_id;
                    $pendaftaran->petugas_tgl_pembuat = date('Y-m-d H:i:s');
                    $pendaftaran->is_multipayer = $dataPendaftaran['is_multipayer'];
                    $pendaftaran->save();
                } else {
                    $pendaftaran = new Pendaftaran();
                    $pendaftaran->attributes = $dataPendaftaran;
                    $pendaftaran->save();
                    $pendaftaranId = $pendaftaran->pendaftaran_id;
                }
            } else {
                $pendaftaran = new Pendaftaran();
                $pendaftaran->attributes = $dataPendaftaran;
                $pendaftaran->save();
                $pendaftaranId = $pendaftaran->pendaftaran_id;
            }

            $prosesBpjs = false;
            if ($isBpjs && !$allowBpjs) {
                $prosesBpjs = true;
                $bpjsRes = $this->saveBpjs($payloadBpjs, $pendaftaranId, $isBbl, true);
                if (is_array($bpjsRes)) return $bpjsRes;
            } else if ($isBpjs && $allowBpjs) { //handle bpjs unauth
                $bpjsRes = $this->saveBpjsWithouBridging($payloadBpjs, $pendaftaranId, $isBbl, true);
                $prosesBpjs = false;
                // sleep(20);
            }

            // Save pasien admisi
            IntegrasiAkunting::integrateKarcisPasien($pendaftaranId);
            $transaction->commit();
            $masukKamar = ArrayHelper::getValue($additionals, 'masuk_kamar');
            $kamarRuanganId = ArrayHelper::getValue($masukKamar, 'kamarruangan_id');
            (new BpjsAplicare())->createOrUpdateAplicare($kamarRuanganId, DocoConstants::TYPE_UPDATE_APLICARE);

            // Select no pendaftaran
            $noPendaftaran = Pendaftaran::findOne($pendaftaranId)->no_pendaftaran;
            $pendaftaran->no_pendaftaran = $noPendaftaran;

            if(Yii::$app->params['isRabbitMq']) {   
                (new RabbitBgProcess())->send([
                    "penjamin_id" => $pendaftaran->penjamin_id,
                    "kodebenefit" => ArrayHelper::getValue($dataPendaftaran, 'referensi_asuransi'),
                    "data_peserta" => ArrayHelper::getValue($dataPendaftaran, 'data_peserta'),
                    "data_pendaftaran" => $pendaftaran->attributes
                ], 'integrate_insurance', 'integrate_insurance');   
            }

            if (!empty($detailTindakan)) {

                // Integrasi kasir tagihan pelayanan
                $tagihanTindakan = (new KasirService)->tagihan([
                    'pendaftaran_id' => $pendaftaranId,
                    'no_pendaftaran' => $noPendaftaran,
                    'instalasi_id' => DocoConstants::INST_ID_RI,
                    'ruangan_id' => $idRuangan,
                    'penjamin_id' => $payload->penjamin_id,
                    'kelas_pelayanan_id' => $idKelasPelayanan
                ], $detailTindakan);

                if (isset($tagihanTindakan['meta']['result']) && $tagihanTindakan['meta']['result'] == 'failed') {
                    $transaction->rollBack();
                    return isset($tagihanTindakan['message']) ? $tagihanTindakan['message'] : 'Terjadi Kesalahan API';
                }
            }

            return [
                'message' => 'Proses Pendaftaran berhasil',
                'id' => DocoHelpers::encrypt($pendaftaranId),
                'is_bpjs' => $prosesBpjs
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/', $e->getMessage(), $out);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = 'Query :' . $out[1];
            }
            if(preg_match('/\bduplicate\b/i', $out[1])) { //class dan max attemp
                if($this->prosesError < self::ATTEMPT) {
                    $this->prosesError++;
                    $this->save($dataPendaftaran , $additionals, $payload, $payloadBpjs, $isBpjs , $allowBpjs, $idKelasPelayanan, $idRuangan, $isBbl, $konfig, $latestMedicalRecord, $detailTindakan);
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => $message,
                    ];
                }
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => $message,
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage(),
            ];
        }
    }
}
