<?php

/**
 *
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;

use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\PenanggungBiaya;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\AsuransiT;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoKunjunganRd;
use app\modules\v1\models\InfoPasienMcuView;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyncsantoyusupR;
use Doco\models\KonfigSystem;
use Doco\models\pendaftaran\PendaftaranMultipayer;
use Doco\models\Pegawai;

use app\modules\v1\payload\EditPendaftaranForm;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\PenanggungBiayaForm;
use Doco\models\kasir\Pembayaran;
use Doco\models\kasir\RecalculateTagihanFn;
use Doco\models\kasir\UbahDokterFn;
use Doco\Services\InternalService;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;
use Doco\Traits\AutoPlafonTrait;

class UpdatePendaftaranProcess extends \Doco\components\DocoBaseProcessExtension
{
    use AutoPlafonTrait;

    protected $komponenTotal;
    protected $kelasPelayananRanap;

    const IGD = 'igd';
    const RANAP = 'ranap';
    const RAJAL = 'rajal';
    const MCU = 'mcu';
    const ST_YUSUP = 'st-yusup';
    const PENUNJANG = 'penunjang';
    const PAKET = 'paket';
    const TINDAKAN = 'tindakan';
    const INST_PENUNJANG = [4, 5, 7, 12];

    protected function buildPayload($value, $model, $dokterDpjpbefore = null, $isChangeDoc = false)
    {
        $dokter_id = $dokterDpjpbefore == $value['dokterpenanggungjawab_id'] ? $model->pegawai_id : '';
        return [
            'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
            'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
            'dokter_id' => $dokter_id,
            'perawat_id' => $value['perawat1_id'],
            'perawat2_id' => $value['perawat2_id'],
            'tipepaket_id' => $value['tipepaket_id'],
            'daftartindakan_id' => $value['daftartindakan_id'],
            'qty' => $value['qty_tindakan'],
            'is_cyto' => $value['cyto_tindakan'],
            'is_penyulit' => $value['penyulit_tindakan'],
            'implementasi_id' => $value['implementasi_id'],
            'instruksitindakan_id' => $value['instruksitindakan_id'],
            'instalasi_id' => $value['instalasi_id'],
            'ruangan_id' => $value['ruangan_id'],
            'kelaspelayanan_id' => $this->kelasPelayananRanap ? $this->kelasPelayananRanap : $value['kelaspelayanan_id'],
            'is_penatajasa' => $value['is_penatajasa'],
            'penjamin_id' => $model->penjamin_id,
            'pendaftaran_id' => $value['pendaftaran_id'],
            'pasienadmisi_id' => $value['pasienadmisi_id'],
            'is_change_doc' => $isChangeDoc,
        ];
    }

    private function deleteSep(array $dataBpjs)
    {
        $model = new Bpjs;
        $model->t_sep = $dataBpjs;
        $result = $model->deleteSep();
        return $result;
    }

    private function deleteDataAsuransi($asuransipasien_id, $pendaftaran_id)
    {
        // hapus asuransipasien_id di pendaftaran
        $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
        if($pendaftaran) {
            $pendaftaran->asuransipasien_id = null;
            $pendaftaran->save();
        }

        // hapus pendaftaran_id di asuransipasien_m
        $asuransipasien = AsuransiPasien::findOne($asuransipasien_id);
        if($asuransipasien) {
            $asuransipasien->pendaftaran_id = null;
            $asuransipasien->save();
        }

        return true;
    }

    private function deleteDataPenanggungBiaya($penanggungbiaya_id, $pendaftaran_id)
    {
        // hapus penanggungbiaya_id di pendaftaran
        $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
        if($pendaftaran) {
            $pendaftaran->penanggungbiaya_id = null;
            $pendaftaran->save();
        }

        return true;
    }

    private function deleteDataBpjs($bpjs_id = null)
    {
        if(!empty($bpjs_id)) {
            $bpjs = Bpjs::findOne($bpjs_id);
            $noSep = !empty($bpjs->nosep) ? $bpjs->nosep : '';
            if($bpjs && $bpjs_id) {
                if($bpjs->delete($bpjs_id)) {
                    $dataBpjs = [
                        'noSep' => $noSep,
                        'user' => Yii::$app->jwt->user->nama_pemakai
                    ];
                    $this->deleteSep($dataBpjs);
                }
            }
        }
    }

    /**
     * Check integrasi pasien dengan vendor / penjamin.
     */
    private function checkIntegrasiPasien($pendaftaranId, $penjaminId)
    {
        $pendaftaranData = Pendaftaran::find()
            ->select([
                'pendaftaran_id',
                'penjamin_id',
                'asuransi_id'
            ])
            ->where(['pendaftaran_id' => $pendaftaranId])->one();
        
        if($penjaminId == $pendaftaranData->penjamin_id) {
            return false;
        }
        
        /**
         * Check apabila integrasi aktif
         */
        $penjaminData = Penjamin::find()
            ->select([
                'penjamin_m.penjamin_id',
                'penjamin_m.penjamin_nama',
                'penjamin_m.konfigasuransi_id',
                'konfigasuransi_k.provider_id'
            ])
            ->where([
                'penjamin_m.penjamin_id' => $pendaftaranData->penjamin_id,
                'penjamin_m.is_deleted' => false,
                'penjamin_m.is_active' => true,
                'konfigasuransi_k.is_deleted' => false,
                'konfigasuransi_k.is_active' => true
            ])
            ->join('INNER JOIN', 'konfigasuransi_k', 'konfigasuransi_k.konfigasuransi_id = penjamin_m.konfigasuransi_id')
            ->asArray()->one();
        
        if(! empty($penjaminData) && isset($pendaftaranData->asuransi_id)) {
            return true;
        }
    
        return false;
    }

	protected function processFlow()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();

        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $transaction = $connection->beginTransaction();
        $errorParse = [];
        $dokterDpjpbefore = null;
        try {
            $konfigSystem = $cache->get(DocoConstants::VAR_K_S);
            if (empty($konfigSystem)) {
                $konfigSystem = KonfigSystem::find()->asArray()->one();
                $cache->set(DocoConstants::VAR_K_S, $konfigSystem);
            }

            $model = new EditPendaftaranForm;
            $modelAsuransi = new AsuransiForm;
            $modelPenanggungBiaya = new PenanggungBiayaForm;
            $modelBpjs = new Bpjs;
            $detailTindakan = array();
            $detailTindakanPenunjang = array();
            $detailTindakanKamar = array();
            $param_rs = isset($post['param_rs']) ? $post['param_rs'] : null;
            $model->attributes = isset($post['kunjungan']) ? $post['kunjungan'] : [];
            $pasienIntegrasi = $this->checkIntegrasiPasien($model->pendaftaran_id, $model->penjamin_id);
            if ($pasienIntegrasi) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => "Pasien terintegrasi dengan penjamin.",
                    'additional' => [
                        'is_integrasi' => true
                    ]
                ]);
            }

            //$this->komponenTotal = $this->constans->actionGetId('komponen_total');
            $isPenunjang = $isChangeDoc = true;
            $this->kelasPelayananRanap = null;
            if (!empty($param_rs)) {
                $model->scenario = ($param_rs != self::ST_YUSUP) ? EditPendaftaranForm::RAJAL_EDIT : EditPendaftaranForm::ST_YUSUP;
            } else {
                $model->scenario = ($model->jenis != self::RANAP) ? EditPendaftaranForm::RAJAL_EDIT : 'default';
            }
            if($model->group_carabayar == DocoConstants::GROUP_JAMINAN) {
                $modelAsuransi->attributes = isset($post['asuransi']) ? $post['asuransi'] : null;

                if (!$modelAsuransi->validate()) {
                    $errorParse['asuransi'] = $modelAsuransi->errors;
                }
            }
            if (!empty($post['penanggungbiaya'])) {
                $modelPenanggungBiaya->attributes = $post['penanggungbiaya'];
                if (!$modelPenanggungBiaya->validate()) $errorParse['penanggungbiaya'] = $modelPenanggungBiaya->errors;
            }
            
            $countPembayaran = Pembayaran::find()
                ->where(['pendaftaran_id' => $model->pendaftaran_id])
                ->andWhere(['is_deleted' => false])
                ->count();

            if ($countPembayaran > 0) {
                $pendaftaran = Pendaftaran::find()->where([
                    'pendaftaran_id' => $model->pendaftaran_id
                ])->one();
                if ($model->carabayar_id != $pendaftaran->carabayar_id || $model->penjamin_id != $pendaftaran->penjamin_id) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => 'Pasien sudah melakukan pembayaran, perubahan hanya dapat dilakukan pada field Dokter DPJP dan Jenis Kasus Penyakit.'
                    ]);
                }
            }
            
            $model->limit_tagihan = 0;
            if(intval($model->group_carabayar) === intval(DocoConstants::GROUP_BPJS)){
                $pendaftaranPelafon = Pendaftaran::find()->where([
                    'pendaftaran_id' => $model->pendaftaran_id
                ])->one();
                if(!empty($pendaftaranPelafon)){
                    $autoPlafon = $this->setAutoPlafon($pendaftaranPelafon->instalasi_id, $pendaftaranPelafon->kelaspelayanan_id, $pendaftaranPelafon->ruangan_id);
                    if (!empty($autoPlafon)) {
                        $model->limit_tagihan = $autoPlafon;
                    }
                }
            }
            
            switch ($model->jenis) {
                case self::RANAP:
                    $dataPendaftaran = InfoPasienRiView::find()->select([
                        'pasienadmisi_id',
                        'no_pendaftaran',
                        'pendaftaran_id',
                        'carabayar_id',
                        'penjamin_id',
                        'kelaspelayanan_id',
                        'ruangan_id',
                        'nama_pasien',
                        'jeniskelamin_id',
                        'status_bayar',
                        'jeniskasuspenyakit_id',
                        'is_close_bill'
                    ])->where([
                        'pasienadmisi_id' => $model->pasienadmisi_id
                    ])->asArray()->one();

                    /** Untuk Update Catatan  */
                    $pendaftaran = Pendaftaran::find()->where([
                        'pendaftaran_id' => $model->pendaftaran_id
                    ])->one();

                    $pendaftaran->keterangan_pendaftaran = $model->keterangan;
                    $pendaftaran->referal_pegawai_id = (int) $model->referal > 0 ? $model->referal : null;
                    $pendaftaran->referal_luar = (int) $model->referal == 0 ? strtoupper($model->referal) : null;
                    if (!$pendaftaran->save()) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $pendaftaran->errors
                        ]);
                    }

                    $modelPendaftaran = PasienAdmisi::find()->where([
                        'pasienadmisi_id' => $model->pasienadmisi_id
                    ])->one();

                    /** Pasien yang sudah pulang dan batal pendaftaran ga bisa di edit */
                    if ($modelPendaftaran->status_ranap === DocoConstants::STATUS_RANAP_BATAL_RAWAT
                        || $modelPendaftaran->status_ranap === DocoConstants::STATUS_RANAP_PULANG) {

                         /** penambahan baca status bayar */
                        if($dataPendaftaran['status_bayar'] == DocoConstants::LUNAS) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_DIPULANGKAN
                            ]);
                        }
                    }

                    $isTitipan = false;
                    $kelasPerawatan = $modelPendaftaran->kelaspelayanan_id;
                    $model->kelaspelayanan_id = $kelasPerawatan;
                    $model->jeniskasuspenyakit_id = $dataPendaftaran['jeniskasuspenyakit_id'];

                    /** Pasien yang belum di periksa dapat pindah kamar */
                    if ($modelPendaftaran->status_ranap !== DocoConstants::STATUS_RANAP_PERIKSA) {
                        $jenisKelamin = isset($dataPendaftaran['jeniskelamin_id']) ? $dataPendaftaran['jeniskelamin_id'] : null;
                        $modelPendaftaran->tgl_admisi = $model->tgl_admisi;
                        $isTitipan = !empty($model->is_pasientitipan) ? $model->is_pasientitipan : $modelPendaftaran->is_pasientitipan;
                        $modelPendaftaran->is_pasientitipan = (bool) $isTitipan;

                        if ((int) $modelPendaftaran->kamartempattidur_id !== (int) $model->kamartempattidur_id) {
                            $modelPendaftaran->ruangan_id = $model->ruangan_id;
                            $modelPendaftaran->kamarruangan_id = $model->kamarruangan_id;
                            $modelPendaftaran->kamartempattidur_id = $model->kamartempattidur_id;
                            $modelPendaftaran->kelaspelayanan_id = $model->kelaspelayanan_id;
                            $kelasPerawatan = $model->kelaspelayanan_id;
                            $qMasukKamar = MasukKamar::find()->where([
                                'pasienadmisi_id' => $modelPendaftaran->pasienadmisi_id
                            ])->one();

                            /** Update Masuk Kamar */
                            if (!empty($qMasukKamar)) {
                                /** Status Kamar */
                                $statusKosong = ($jenisKelamin == DocoConstants::VAR_PR)
                                                            ? DocoConstants::VAR_KTTKP : DocoConstants::VAR_KTTKL;
                                $statusIsi = ($jenisKelamin == DocoConstants::VAR_PR)
                                                            ? DocoConstants::VAR_KTTIP : DocoConstants::VAR_KTTIL;

                                /**  Update kamar Sebelum nya */
                                KamarTempatTidur::updateAll([
                                    'status_isi' => false,
                                    'kettempattidur_id' => $statusKosong,
                                ],[
                                    'kamartempattidur_id' => $qMasukKamar->kamartempattidur_id
                                ]);

                                /** Update Kamar baru */
                                KamarTempatTidur::updateAll([
                                    'status_isi' => true,
                                    'kettempattidur_id' => $statusIsi,
                                ],[
                                    'kamartempattidur_id' => $model->kamartempattidur_id
                                ]);

                                $qMasukKamar->ruangan_id = $model->ruangan_id;
                                $qMasukKamar->carabayar_id = $model->carabayar_id;
                                $qMasukKamar->penjamin_id = $model->penjamin_id;
                                $qMasukKamar->pegawai_id = $model->pegawai_id;
                                $qMasukKamar->kelaspelayanan_id = $model->kelaspelayanan_id;
                                $qMasukKamar->kamartempattidur_id = $model->kamartempattidur_id;
                                $qMasukKamar->kamarruangan_id = $model->kamarruangan_id;
                                $qMasukKamar->tgl_masukkamar = date('Y-m-d H:i:s', strtotime($model->tgl_admisi));
                                $qMasukKamar->jam_masukkamar = date('H:i:s', strtotime($model->tgl_admisi));

                                if (!$qMasukKamar->save()) {
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                        'data' => $qMasukKamar->attributes
                                    ]);
                                }
                            }
                        }
                    } else {
                        $model->scenario = EditPendaftaranForm::RANAP_PERIKSA;
                    }

                    $isAps = !empty($model->is_aps) ? $model->is_aps : $modelPendaftaran->is_aps;
                    if ($isTitipan && $modelPendaftaran->bpjs_id && empty($isAps)) {
                        $qBpjs = Bpjs::find()->select([
                            'bpjs_id',
                            'klsrawat',
                        ])->where([
                            'bpjs_id' => $modelPendaftaran->bpjs_id
                        ])->asArray()->one();
                        if (!empty($qBpjs)) {
                            $kelasPerawatan = $qBpjs['klsrawat'];
                        }
                    }

                    $modelPendaftaran->carabayar_id = $model->carabayar_id;
                    $modelPendaftaran->penjamin_id = $model->penjamin_id;
                    $modelPendaftaran->is_aps = $isAps;
                    // $modelPendaftaran->pegawai_id = $model->pegawai_id;
                    $modelPendaftaran->keterangan = $model->keterangan;
                    $modelPendaftaran->limit_tagihan = $model->limit_tagihan;
                    $this->kelasPelayananRanap = $modelPendaftaran->is_pasientitipan ? $modelPendaftaran->kelas_ditagihkan_id : $model->kelaspelayanan_id;
                    $isChangeDoc = false;
                    break;
                default:
                    $isTitipan = false;
                    if ($model->jenis === self::IGD) {
                        $dataPendaftaran = InfoKunjunganRd::find()->select([
                            'pendaftaran_id',
                            'no_pendaftaran',
                            'carabayar_id',
                            'penjamin_id',
                            'kelaspelayanan_id',
                            'ruangan_id',
                            'nama_pasien',
                            'jeniskelamin as jeniskelamin_id',
                            'status_periksa_id as status_periksa',
                            'status_bayar',
                            'is_close_bill'
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->asArray()->one();
                        $isChangeDoc = false;
                    } else if ($model->jenis === self::MCU) {
                        $dataPendaftaran = InfoPasienMcuView::find()->select([
                            'pendaftaran_id',
                            'no_pendaftaran',
                            'carabayar_id',
                            'penjamin_id',
                            'kelaspelayanan_id',
                            'ruangan_id',
                            'nama_pasien',
                            'jeniskelamin as jeniskelamin_id',
                            'status_periksa',
                            'status_bayar',
                            'is_close_bill'
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->asArray()->one();
                    } else if ($model->jenis === self::PENUNJANG) {
                        $dataPendaftaran = InfPasienPenunjang::find()->select([
                            'pendaftaran_id',
                            'no_pendaftaran',
                            'carabayar_id',
                            'penjamin_id',
                            'kelaspelayanan_id',
                            'ruangan_id',
                            'nama_pasien',
                            'jeniskelamin as jeniskelamin_id',
                            'status_periksa',
                            'status_bayar',
                            'is_close_bill'
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->asArray()->one();

                        $modelPenunjang = PasienMasukPenunjang::find()
                        ->where(['pendaftaran_id' => $model->pendaftaran_id])
                        ->one();
                        $modelPenunjang->pegawai_id = $model->pegawai_id;
                        $modelPenunjang->jeniskasuspenyakit_id = $model->jeniskasuspenyakit_id;
                        $modelPenunjang->save();
                        $isPenunjang = true;
                    } else {
                        $dataPendaftaran = InfoKunjunganRajal::find()->select([
                            'pendaftaran_id',
                            'no_pendaftaran',
                            'carabayar_id',
                            'penjamin_id',
                            'kelaspelayanan_id',
                            'ruangan_id',
                            'nama_pasien',
                            'jeniskelamin as jeniskelamin_id',
                            'status_periksa',
                            'status_bayar',
                            'is_close_bill'
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->andWhere(['jenis'=>'PENDAFTARAN'])->asArray()->one();
                    }

                    $statusPulang = isset($dataPendaftaran['status_periksa']) ? $dataPendaftaran['status_periksa'] : null;

                    if ($statusPulang === DocoConstants::STATUS_PERIKSA_PULANG
                            || $statusPulang === DocoConstants::STATUS_PERIKSA_BTL_PERIKSA
                            || $statusPulang === DocoConstants::STATUS_PERIKSA_BTL_KUNJ) {

                        /** penambahan baca status bayar */
                        if($dataPendaftaran['status_bayar'] == DocoConstants::LUNAS) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_DIPULANGKAN
                            ]);
                        }
                    }

                    $modelPendaftaran = Pendaftaran::find()->where([
                        'pendaftaran_id' => $model->pendaftaran_id
                    ])->one();

                    $modelPendaftaran->penjamin_id = $model->penjamin_id;
                    // $modelPendaftaran->pegawai_id = $model->pegawai_id;
                    $modelPendaftaran->carabayar_id = $model->carabayar_id;
                    $modelPendaftaran->ruangan_id = (!empty($model->ruangan_id)) ? $model->ruangan_id : $dataPendaftaran['ruangan_id'];
                    // $modelPendaftaran->pegawai_id = $model->pegawai_id;
                    $modelPendaftaran->jeniskasuspenyakit_id = $model->jeniskasuspenyakit_id;
                    $modelPendaftaran->keterangan_pendaftaran = $model->keterangan;
                    $modelPendaftaran->limit_tagihan = $model->limit_tagihan;
                    $kelasPerawatan = $dataPendaftaran['kelaspelayanan_id'];

                    $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                    $petugas_id = $jwt->loginpemakai_id;
                    $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                    $modelPendaftaran->petugas_id = $petugas_id;
                    $modelPendaftaran->petugas_tgl_pembuat = $petugas_tgl_pembuat;

                    $modelPendaftaran->referal_pegawai_id = (int) $model->referal > 0 ? $model->referal : null;
                    $modelPendaftaran->referal_luar = (int) $model->referal == 0 ? strtoupper($model->referal) : null;

                    break;
            }

            if (!$model->validate()) {
                $errorParse['kunjungan'] = $model->errors;
            }

            if (!empty($errorParse)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $errorParse
                ]);
            }

            if (empty($dataPendaftaran)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => DocoMessages::ERR_MESSAGE_TIDAK_SESUAI
                ]);
            }

            /**
             * ? penambahan pengecekan lock bill
             */
            $isCloseBill = isset($dataPendaftaran['is_close_bill']) ? $dataPendaftaran['is_close_bill'] : false;
            if ($isCloseBill) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Pasien sudah dilakukan proses Lock Bill.'
                ]);
            }

            $changeTindakan = false;
            $noRegist = isset($dataPendaftaran['no_pendaftaran']) ? $dataPendaftaran['no_pendaftaran'] : null;
            if ($modelPendaftaran->pegawai_id != $model->pegawai_id) {
                $dokterDpjpbefore = $modelPendaftaran->pegawai_id;
                $modelPendaftaran->pegawai_id = $model->pegawai_id;
                $changeTindakan = $isChangeDoc;
            }
            /** Check Kondisi perubahan Cara bayar */
            if ((int) $dataPendaftaran['penjamin_id'] !== (int) $model->penjamin_id) {
                $changeTindakan = true;
                switch ($model->group_carabayar) {
                    case DocoConstants::GROUP_BPJS :
                        $namaPasien = !empty($dataPendaftaran['nama_pasien']) ? $dataPendaftaran['nama_pasien'] : null;
                        if ($konfigSystem['is_sep_mandatory_on_edit'] && empty($model->nosep)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_SEP_REQUIRED
                            ]);
                        }

                        $pendaftaranRanap = Pendaftaran::find()->select([
                            'COALESCE(prev_pendaftaran_id, '.new \yii\db\Expression("(additional_data::json->>'pendaftaranasal_id')::integer").' ) AS prev_pendaftaran_id'
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->scalar();

                        if(!empty($pendaftaranRanap)) {
                            $qBpjs = Bpjs::find()->where(['nosep' => $model->nosep])
                                ->andWhere(['not', ['pendaftaran_id' => $pendaftaranRanap]])
                                ->one();
                        } else {
                            $qBpjs = Bpjs::find()->where(['nosep' => $model->nosep])
                                ->andWhere(['not', ['pendaftaran_id' => $model->pendaftaran_id]])
                                ->one();
                        }

                        if (!empty($qBpjs) && !empty($model->nosep)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_SEP_EXIST
                            ]);
                        }
                        /** Save to cache for respose find SEP */
                        if (!empty($model->nosep)) {
                            $findSep = $cache->get($model->nosep);
                            if (empty($findSep)) {
                                $findSep = (new Bpjs)->referensiCariSep($model->nosep);
                            }
                        }

                        if (isset($findSep['metaData']['code'])) {
                            if ($findSep['metaData']['code'] == 200) {
                                $cache->set($model->nosep, $findSep);
                                $klsrawat = null;
                                $responseSep = isset($findSep['response']) ? $findSep['response'] : [];
                                $diagNama = isset($responseSep['diagnosa']) ? $responseSep['diagnosa'] : null;
                                $noRujukan = isset($responseSep['noRujukan']) ? $responseSep['noRujukan'] : null;
                                $tglRujukan = isset($responseSep['tglSep']) ? $responseSep['tglSep'] : null;
                                $peserta = isset($responseSep['peserta']) ? $responseSep['peserta'] : [];
                                $namaPeserta = isset($peserta['nama']) ? $peserta['nama'] : [];
                                $noKartu = isset($peserta['noKartu']) ? $peserta['noKartu'] : '';

                                if (!empty($noKartu)) {
                                    $findPeserta = (new Bpjs)->peserta($noKartu);
                                    $resPeserta = isset($findPeserta['response']['peserta'])
                                                ? $findPeserta['response']['peserta'] : [];
                                    $klsrawat = isset($resPeserta['hakKelas']) ? $resPeserta['hakKelas'] : null;
                                } else {
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => DocoMessages::ERR_MESSAGE_BPJS_EMPTY
                                    ]);
                                }

                                if (strtolower($namaPasien) !== strtolower($namaPeserta) && empty($model->allow_bpjs)) {
                                    $messages = "Nama Pasien yang diinputkan berbeda dengan Nama BPJS<br>";
                                    $messages .= "Nama Pasien BPJS : <strong>{$namaPeserta}</strong><br>";
                                    $messages .= "Pasien yang terdaftar : <strong>{$namaPasien}</strong><br>";
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => $messages,
                                        'additional' => [
                                            'is_bpjs' => true
                                        ]
                                    ]);
                                }

                                $modelBpjs = new Bpjs;
                                $modelBpjs->tglsep = $tglRujukan;
                                $modelBpjs->nosep = $model->nosep;
                                $modelBpjs->nokartuasuransi = $noKartu;
                                $modelBpjs->tglrujukan = $tglRujukan;
                                $modelBpjs->norujukan = $noRujukan;
                                $modelBpjs->ppkrujukan = '-';
                                $modelBpjs->jnspelayanan = isset($responseSep['kelasRawat']) ? $responseSep['kelasRawat'] : null;
                                $modelBpjs->diagnosaawal = $diagNama;
                                $modelBpjs->politujuan = isset($responseSep['poli']) ? $responseSep['poli'] : null;
                                $modelBpjs->lakalantas = 0;
                                $modelBpjs->pendaftaran_id = $model->pendaftaran_id;
                                $modelBpjs->pasienadmisi_id = $model->pasienadmisi_id;
                                $modelBpjs->klsrawat = $klsrawat;
                                $modelBpjs->additional_data = json_encode([
                                    'sep' => $responseSep
                                ]);
                                if ($modelBpjs->save(false)) {
                                    $modelPendaftaran->bpjs_id = $modelBpjs->bpjs_id;
                                    $rujukan = new Rujukan;
                                    $diagnosa = Diagnosa::find()
                                        ->select([
                                            'diagnosa_id',
                                            'diagnosa_kode',
                                        ])->where(['diagnosa_nama' => $diagNama])
                                        ->asArray()
                                        ->one();
                                    $rujukan->asalrujukan_id = 2;
                                    $rujukan->rujukandari_id = 1;
                                    $rujukan->no_rujukan = $noRujukan;
                                    $rujukan->nama_perujuk = 'BPJS';
                                    $rujukan->tanggal_rujukan = $tglRujukan;
                                    $rujukan->kodediagnosa_rujukan = isset($diagnosa['diagnosa_kode'])
                                                                            ? $diagnosa['diagnosa_kode'] : null;
                                    $rujukan->diagnosa_id = isset($diagnosa['diagnosa_id'])
                                                                            ? $diagnosa['diagnosa_id'] : null;
                                    $rujukan->save(false);
                                }
                            } else {
                                $transaction->rollBack();
                                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                    'text' => DocoMessages::ERR_MESSAGE_BPJS_SERVER
                                ]);
                            }
                        }

                        $this->deleteDataAsuransi($modelPendaftaran->asuransipasien_id, $model->pendaftaran_id);

                        break;
                    case DocoConstants::GROUP_JAMINAN :
                        $this->deleteDataBpjs($modelPendaftaran->bpjs_id);
                        $modelPendaftaran->bpjs_id = null;
                        break;
                    default:
                        /** Sebagai Umum */
                        $this->deleteDataBpjs($modelPendaftaran->bpjs_id);
                         $modelPendaftaran->bpjs_id = null;
                        $this->deleteDataAsuransi($modelPendaftaran->asuransipasien_id, $model->pendaftaran_id);

                        break;
                }
            } else { //kondisi tidak merubah carabayar
                switch ($model->group_carabayar) {
                    case DocoConstants::GROUP_BPJS:
                        if ($konfigSystem['is_sep_mandatory_on_edit'] && empty($model->nosep)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_SEP_REQUIRED
                            ]);
                        }

                        $cekPasienSep = Bpjs::find()->where(['nosep' => $model->nosep])
                        ->andWhere(['pendaftaran_id' => $model->pendaftaran_id])
                        ->one();

                        if(empty($cekPasienSep)) {
                            $pendaftaranRanap = Pendaftaran::find()->select([
                                'pendaftaran_id',
                            ])->orWhere([
                                'prev_pendaftaran_id' => $model->pendaftaran_id
                            ])->orWhere(
                                new \yii\db\Expression("(additional_data::json->>'pendaftaranasal_id')::integer = " . $model->pendaftaran_id)
                            )->scalar();
                            if(empty($pendaftaranRanap)) {
                                $qBpjs = Bpjs::find()->where(['nosep' => $model->nosep])
                                    ->one();
                            } else {
                                $qBpjs = Bpjs::find()->where(['nosep' => $model->nosep])
                                    ->andWhere(['not', ['pendaftaran_id' => $model->pendaftaran_id]])
                                    ->one();
                            }

                            if (!empty($qBpjs) && !empty($model->nosep)) {
                                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                    'text' => DocoMessages::ERR_MESSAGE_SEP_EXIST
                                ]);
                            }
                            /** Save to cache for respose find SEP */
                            if (!empty($model->nosep)) {
                                $findSep = $cache->get($model->nosep);
                                if (empty($findSep)) {
                                    $findSep = (new Bpjs)->referensiCariSep($model->nosep);
                                }
                            }

                            if (isset($findSep['metaData']['code'])) {
                                if ($findSep['metaData']['code'] == 200) {
                                    $cache->set($model->nosep, $findSep);
                                    $klsrawat = null;
                                    $responseSep = isset($findSep['response']) ? $findSep['response'] : [];
                                    $diagNama = isset($responseSep['diagnosa']) ? $responseSep['diagnosa'] : null;
                                    $noRujukan = isset($responseSep['noRujukan']) ? $responseSep['noRujukan'] : null;
                                    $tglRujukan = isset($responseSep['tglSep']) ? $responseSep['tglSep'] : null;
                                    $peserta = isset($responseSep['peserta']) ? $responseSep['peserta'] : [];
                                    $namaPeserta = isset($peserta['nama']) ? $peserta['nama'] : [];
                                    $noKartu = isset($peserta['noKartu']) ? $peserta['noKartu'] : '';

                                    if (!empty($noKartu)) {
                                        $findPeserta = (new Bpjs)->peserta($noKartu);
                                        $resPeserta = isset($findPeserta['response']['peserta'])
                                                    ? $findPeserta['response']['peserta'] : [];
                                        $klsrawat = isset($resPeserta['hakKelas']) ? $resPeserta['hakKelas'] : null;
                                    } else {
                                        $transaction->rollBack();
                                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                            'text' => DocoMessages::ERR_MESSAGE_BPJS_EMPTY
                                        ]);
                                    }

                                    $modelBpjs = new Bpjs;
                                    $modelBpjs->tglsep = $tglRujukan;
                                    $modelBpjs->nosep = $model->nosep;
                                    $modelBpjs->nokartuasuransi = $noKartu;
                                    $modelBpjs->tglrujukan = $tglRujukan;
                                    $modelBpjs->norujukan = $noRujukan;
                                    $modelBpjs->ppkrujukan = '-';
                                    $modelBpjs->jnspelayanan = isset($responseSep['kelasRawat']) ? $responseSep['kelasRawat'] : null;
                                    $modelBpjs->diagnosaawal = $diagNama;
                                    $modelBpjs->politujuan = isset($responseSep['poli']) ? $responseSep['poli'] : null;
                                    $modelBpjs->lakalantas = 0;
                                    $modelBpjs->pendaftaran_id = $model->pendaftaran_id;
                                    $modelBpjs->pasienadmisi_id = $model->pasienadmisi_id;
                                    $modelBpjs->klsrawat = $klsrawat;
                                    $modelBpjs->additional_data = json_encode([
                                        'sep' => $responseSep
                                    ]);
                                    if ($modelBpjs->save(false)) {
                                        $modelPendaftaran->bpjs_id = $modelBpjs->bpjs_id;
                                        $rujukan = new Rujukan;
                                        $diagnosa = Diagnosa::find()
                                            ->select([
                                                'diagnosa_id',
                                                'diagnosa_kode',
                                            ])->where(['diagnosa_nama' => $diagNama])
                                            ->asArray()
                                            ->one();
                                        $rujukan->asalrujukan_id = 2;
                                        $rujukan->rujukandari_id = 1;
                                        $rujukan->no_rujukan = $noRujukan;
                                        $rujukan->nama_perujuk = 'BPJS';
                                        $rujukan->tanggal_rujukan = $tglRujukan;
                                        $rujukan->kodediagnosa_rujukan = isset($diagnosa['diagnosa_kode'])
                                                                                ? $diagnosa['diagnosa_kode'] : null;
                                        $rujukan->diagnosa_id = isset($diagnosa['diagnosa_id'])
                                                                                ? $diagnosa['diagnosa_id'] : null;
                                        $rujukan->save(false);
                                    }
                                } else {
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => DocoMessages::ERR_MESSAGE_BPJS_SERVER
                                    ]);
                                }
                            }
                        } else {
                            $pegawai = Pegawai::find()->select(['kode_dokter_bpjs', 'nama_dokter_bpjs'])->where(['pegawai_id' => ArrayHelper::getValue($post, 'kunjungan.pegawai_id')])->one();
                            if (ArrayHelper::getValue($pegawai, 'kode_dokter_bpjs')) {
                                $data_sep = (new Bpjs)->referensiCariSep(ArrayHelper::getValue($post, 'kunjungan.nosep'));
                                if (ArrayHelper::getValue($data_sep, 'metaData.code', 500) != 200) {
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => ArrayHelper::getValue($data_sep, 'metaData.message')
                                    ]);
                                }

                                $data_peserta = (new Bpjs)->peserta(ArrayHelper::getValue($data_sep, 'response.peserta.noKartu'), ArrayHelper::getValue($data_sep, 'reseponse.tglSep'));
                                if (ArrayHelper::getValue($data_peserta, 'metaData.code', 500) != 200) {
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => ArrayHelper::getValue($data_peserta, 'metaData.message')
                                    ]);
                                }

                                $data_diagnosa = (new Bpjs)->referensiDiagnosa(ArrayHelper::getValue($data_sep, 'response.diagnosa'));
                                if (ArrayHelper::getValue($data_diagnosa, 'metaData.code', 500) != 200) {
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => ArrayHelper::getValue($data_diagnosa, 'metaData.message')
                                    ]);
                                }
                                $jnsPelayanan = ArrayHelper::getValue($data_sep, 'response.jnsPelayanan');
                                if($jnsPelayanan!='Rawat Inap'){
                                    $data_referensi_poli = (new Bpjs)->referensiPoli(ArrayHelper::getValue($data_sep, 'response.poli'));
    
                                    if (ArrayHelper::getValue($data_referensi_poli, 'metaData.code', 500) != 200) {
                                        $transaction->rollBack();
                                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                            'text' => ArrayHelper::getValue($data_referensi_poli, 'metaData.message')
                                        ]);
                                    }
    
                                    $referensi_poli = array_values(array_filter(ArrayHelper::getValue($data_referensi_poli, 'response.poli', []), function ($var) use ($data_sep) {
                                            return ($var['nama'] == ArrayHelper::getValue($data_sep, 'response.poli'));
                                        }));
                                }
                                $payload_sep = [
                                    'request' => [
                                        't_sep' => [
                                            'noSep' => $this->replaceNullToString($data_sep, 'response.noSep', ''),
                                            'klsRawat' => [
                                                'klsRawatHak' => $this->replaceNullToString($data_sep, 'response.klsRawat.klsRawatHak', ''),
                                                'klsRawatNaik' => $this->replaceNullToString($data_sep, 'response.klsRawat.klsRawatNaik', ''),
                                                'pembiayaan' => $this->replaceNullToString($data_sep, 'response.klsRawat.pembiayaan', ''),
                                                'penanggungJawab' => $this->replaceNullToString($data_sep, 'response.klsRawat.penanggungJawab', '')
                                            ],
                                            'noMR' => $this->replaceNullToString($data_peserta, 'response.peserta.mr.noMR', ''),
                                            'catatan' => $this->replaceNullToString($data_sep, 'response.catatan', ''),
                                            'diagAwal' => $this->replaceNullToString($data_diagnosa, 'response.diagnosa.0.kode', ''),
                                            'poli' => [
                                                'tujuan' => $jnsPelayanan=='Rawat Inap'?'': $this->replaceNullToString($referensi_poli, '0.kode', ''),
                                                'eksekutif' => $jnsPelayanan=='Rawat Inap'?'':$this->replaceNullToString($data_sep, 'response.poliEksekutif', '0'),
                                            ],
                                            'cob' => [
                                                'cob' => $this->replaceNullToString($data_sep, 'response.cob', '0'),
                                            ],
                                            'katarak' => [
                                                'katarak' => $this->replaceNullToString($data_sep, 'response.katarak', '0')
                                            ],
                                            'jaminan' => [
                                                'lakaLantas' => $this->replaceNullToString($data_sep, 'response.kdStatusKecelakaan', '0'),
                                                'penjamin' => [
                                                    'tglKejadian' => $this->replaceNullToString($data_sep, 'response.lokasiKejadian.tglKejadian', ''),
                                                    'keterangan' => $this->replaceNullToString($data_sep, 'response.lokasiKejadian.ketKejadian', ''),
                                                    'suplesi' => [
                                                        'suplesi' => $this->replaceNullToString($data_sep, 'response.suplesi', '0'), // dari get sep bpjs belum ada
                                                        'noSepSuplesi' => $this->replaceNullToString($data_sep, 'response.noSepSuplesi', ''), // dari get sep bpjs belum ada
                                                        'lokasiLaka' => [
                                                            'kdPropinsi' => $this->replaceNullToString($data_sep, 'response.lokasiKejadian.kdProp', ''),
                                                            'kdKabupaten' => $this->replaceNullToString($data_sep, 'response.lokasiKejadian.kdKab', ''),
                                                            'kdKecamatan' => $this->replaceNullToString($data_sep, 'response.lokasiKejadian.kdKec', ''),
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            'dpjpLayan' => $this->replaceNullToString($pegawai, 'kode_dokter_bpjs'), // Data yang diubah, sisanya default
                                            'noTelp' => $this->replaceNullToString($data_peserta, 'response.peserta.mr.noTelepon', ''),
                                            'user' => Yii::$app->jwt->user->nama_pemakai
                                        ]
                                    ]
                                ];
                                $cekPasienSep->kode_dpjp_melayani = ArrayHelper::getValue($pegawai, 'kode_dokter_bpjs');
                                $cekPasienSep->nama_dpjp_melayani = ArrayHelper::getValue($pegawai, 'nama_dokter_bpjs');
                                $update_sep = $cekPasienSep->updateSep($payload_sep);
                                if (ArrayHelper::getValue($update_sep, 'metaData.code', 500) == '200') {
                                    $cekPasienSep->save(false);
                                } else {
                                    /**
                                     * perlu dibuat konfigurasi skip
                                    $transaction->rollBack();
                                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                        'text' => ArrayHelper::getValue($update_sep, 'metaData.message')
                                    ]);
                                     */
                                    $cekPasienSep->save(false);
                                }
                            } else {
                                  $transaction->rollBack();
                                  return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                      'text' => 'DPJP belum dimapping kode dokter bpjs'
                                  ]);
                            }
                        }

                        break;

                    default:

                        break;
                }
            }

            if(!empty($post['asuransi'])) {
                $asuransipasien_id = $modelAsuransi->asuransipasien_id;
                $asuransi = (!empty($asuransipasien_id)) ?
                    AsuransiPasien::findOne($asuransipasien_id) : new AsuransiPasien;

                $asuransi->attributes = $modelAsuransi->attributes;
                $asuransi->kelastanggunganasuransi_id = $modelAsuransi->kelastanggungan_id;
                $asuransi->pasien_id = $modelPendaftaran->pasien_id;
                $asuransi->penjamin_id = $model->penjamin_id;
                $asuransi->carabayar_id = $model->carabayar_id;
                $asuransi->pendaftaran_id = $model->pendaftaran_id;

                if($asuransi->validate()) {
                    if($asuransi->save()) {
                        $asuransipasien_id = !empty($asuransipasien_id) ? $asuransipasien_id : $asuransi->asuransipasien_id;
                        $modelPendaftaran->asuransipasien_id = $asuransipasien_id;
                    }
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $asuransi->errors
                    ]);
                }
            }

            if(!empty($post['penanggungbiaya'])) {
                $penanggungbiaya_id = $modelPenanggungBiaya->penanggungbiaya_id;
                $penanggungbiaya = (!empty($penanggungbiaya_id)) ?
                    PenanggungBiaya::findOne($penanggungbiaya_id) : new PenanggungBiaya;

                $penanggungbiaya->attributes = $modelPenanggungBiaya->attributes;
                $penanggungbiaya->pasien_id = $modelPendaftaran->pasien_id;
                $penanggungbiaya->carabayar_id = $model->carabayar_id;

                if($penanggungbiaya->validate()) {
                    if($penanggungbiaya->save()) {
                        $penanggungbiaya_id = !empty($penanggungbiaya_id) ? $penanggungbiaya_id : $penanggungbiaya->penanggungbiaya_id;
                        $modelPendaftaran->penanggungbiaya_id = $penanggungbiaya_id;
                    }
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $penanggungbiaya->errors
                    ]);
                }
            } else {
                $penanggungbiaya_id = isset($modelPendaftaran->penanggungbiaya_id)? $modelPendaftaran->penanggungbiaya_id : null;
                if (!empty($penanggungbiaya_id)){
                    $this->deleteDataPenanggungBiaya($modelPendaftaran->penanggungbiaya_id, $model->pendaftaran_id);
                }
            }

            $listEditTindakan = [];
            // Rollback tindakan ke penjamin baru
            if ($changeTindakan) {
                $idAdmisi = !empty($model->pasienadmisi_id) ? $model->pasienadmisi_id : null;
                $idHistoryTindakan = array();

                $qryRiwayatTindakan = TindakanPelayanan::find();
                if($isPenunjang == false) {
                    $qryRiwayatTindakan = $qryRiwayatTindakan->where([
                        'NOT IN', 'instalasi_id', DocoConstants::INST_ID_BEDAH
                    ]);
                }
                $qryRiwayatTindakan = $qryRiwayatTindakan->andWhere([
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $idAdmisi,
                    'tindakansudahbayar_id' => null
                ]);

                $riwayatTindakan = $qryRiwayatTindakan->asArray()->all();
                if (!empty($riwayatTindakan)) {
                    foreach ($riwayatTindakan as $key => $value) {
                        $listEditTindakan[] = $this->buildPayload($value,$model,$dokterDpjpbefore, $isChangeDoc);
                    }
                }
            }
            if($dataPendaftaran['carabayar_id'] != $model->carabayar_id ) {
                (new InternalService)->sendTo([
                    'Lis' => [
                        'BridgingLisCancelOrder' => [
                                'pendaftaran_id' => $modelPendaftaran->pendaftaran_id,
                                'carabayar_id' => $modelPendaftaran->carabayar_id,
                                'penjamin_lama' => $dataPendaftaran['penjamin_id']
                            ]
                        ]
                ], true);
            }

            if($dataPendaftaran['carabayar_id'] == $model->carabayar_id ) {
               if($dataPendaftaran['penjamin_id'] != $model->penjamin_id) {
                    (new InternalService)->sendTo([
                        'Lis' => [
                            'BridgingLisCancelOrder' => [
                                    'pendaftaran_id' => $modelPendaftaran->pendaftaran_id,
                                    'penjamin_id' => $modelPendaftaran->penjamin_id,
                                    'carabayar_id' => $modelPendaftaran->carabayar_id
                                ]
                            ]
                    ], true);
                }
            }

            $countPembayaran = Pembayaran::find()
            ->where(['pendaftaran_id' => $model->pendaftaran_id])
            ->andWhere(['is_deleted' => false])
            ->count();

            if ($countPembayaran <= 0 || $model->jenis != self::IGD) {
                
                if (!empty($listEditTindakan)) {
                    /**
                     * Handle Recalculate from Store Procedure
                     */
                    $calculate = (new RecalculateTagihanFn([
                        'extParam' => [
                            $modelPendaftaran->pendaftaran_id,
                            $modelPendaftaran->penjamin_id ? $modelPendaftaran->penjamin_id : 0,
                            Yii::$app->jwt->user->loginpemakai_id
                        ]
                    ]))->find()->asArray()->one();
                                            
                    if(isset($calculate['status']) && $calculate['status'] != 0) {
                        Yii::$app->response->statusCode = 500;
                        $transaction->rollBack();
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                            'text' => $calculate['message'],
                        ]); 
                    } 

                    if($isChangeDoc) {
                        $changeDoc = (new UbahDokterFn([
                            'extParam' => [
                                $modelPendaftaran->pendaftaran_id,
                                $modelPendaftaran->pegawai_id,
                                Yii::$app->jwt->user->loginpemakai_id
                            ]
                        ]))->find()->asArray()->one();
                        if (isset($changeDoc['status']) && $changeDoc['status'] != 0) {
                            $transaction->rollBack();
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                                'text' => $calculate['message'],
                            ]); 
                        }
                    }
                }
            }

            $modelPendaftaran->save();

            $antrian = Antrian::find()
            ->where([
                'pendaftaran_id' => $modelPendaftaran->pendaftaran_id,
                'is_konsulpoli' => false,
                'jenisantrian_id' => DocoConstants::VAR_JA_P,
            ])->orderBy(['antrian_id' => SORT_DESC]);
            
            $antrian = $antrian->one();
            
            if (array_key_exists('nomor_urut', $post['kunjungan']) && $post['kunjungan']['nomor_urut'] && $antrian) {
                // Ganti no urut poli sesuai dari inputan
                $antrian->no_antrian = $post['kunjungan']['nomor_urut'];
                $antrian->pegawai_id = $modelPendaftaran->pegawai_id;
                $antrian->save();
            } else if($antrian){
                // Penyesuaian nomor urut antrian poli ketika edit dokter pada hari yang sama dengan tanggal pendaftaran
                if ($antrian->pegawai_id != $modelPendaftaran->pegawai_id && date('Y-m-d', strtotime($modelPendaftaran->tgl_pendaftaran)) == date('Y-m-d')) {
                    $antrian->no_antrian = null;
                    $antrian->is_deleted = true;
                    $antrian->is_active = false;
                    $antrian->deleted_date = date('Y-m-d H:i:s');
                    $antrian->save(false);
                    
                    $jadwaldokter = InfoJadwalDokterView::find()
                        ->where(['and', ['pegawai_id' => $modelPendaftaran->pegawai_id, 'hari_jadwalbuka' => DocoHelpers::getIdHariIni()]])
                        ->orderBy('waktu_selesai DESC')->asArray()->all();
                    if (count($jadwaldokter) > 1){
                        $jadwaldokterfilter = array_values(array_filter($jadwaldokter, function($var){
                                return strtotime($var['waktu_mulai']) <= strtotime(date('H:i:s')) && strtotime($var['waktu_selesai']) <= strtotime(date('H:i:s'));
                            }));

                        $jadwaldokter = count($jadwaldokterfilter) > 0 ? $jadwaldokterfilter : $jadwaldokter;
                    }
                    $konfig_antrian = KonfigAntrian::find()->where([
                        'ruangan_id' => $modelPendaftaran->ruangan_id,
                        'pegawai_id' => $modelPendaftaran->pegawai_id,
                        'jenisantrian_id' => DocoConstants::VAR_JA_P,
                    ])->one();
                    
                    $is_bpjs = $model->group_carabayar == DocoConstants::GROUP_BPJS ? true : false;

                    $new_antrian = new Antrian;
                    $data_antrian = [
                        'ruangan_id' => $modelPendaftaran->ruangan_id,
                        'carabayar_id' => $modelPendaftaran->carabayar_id,
                        'tgl_antrian' => date('Y-m-d H:i:s'),
                        'no_antrian' => '000',
                        'jenisantrian_id' => DocoConstants::VAR_JA_P,
                        'status_antrian' => DocoConstants::ANTRIAN_STATUS_BELUM_PANGGIL,
                        'status_pasien' => DocoConstants::VAR_PAS_B,
                        'pendaftaran_id' => $modelPendaftaran->pendaftaran_id,
                        'pasien_id' => $modelPendaftaran->pasien_id,
                        'penjamin_id' => $modelPendaftaran->penjamin_id,
                        'pegawai_id' => $modelPendaftaran->pegawai_id,
                        'jadwaldokter_id' => ArrayHelper::getValue($jadwaldokter, '0.jadwaldokter_id'),
                        'konfigantrian_id' => ArrayHelper::getValue($konfig_antrian, 'konfigantrian_id'),
                        'estimasidilayani' => (new InfoJadwalDokterView)->getEstimasiByJadwal(ArrayHelper::getValue($jadwaldokter, '0.jadwaldokter_id'), date('Y-m-d H:i:s'), $is_bpjs, true),
                    ];
                    $new_antrian->attributes = $data_antrian;
                    $new_antrian->save();
                    $pendaftaran_antrian = Pendaftaran::find()->where(['pendaftaran_id' => $modelPendaftaran->pendaftaran_id])->one();
                    $pendaftaran_antrian->antrian_id = $new_antrian->antrian_id;
                    $pendaftaran_antrian->save();
                }
            }

            $pendaftaran_multipayer = PendaftaranMultipayer::find()->where(['pendaftaran_id' => $model->pendaftaran_id])->one();
            if ($pendaftaran_multipayer) {
                $asuransi_multipayer = AsuransiPasien::find()->where(['asuransipasien_id' => $pendaftaran_multipayer->asuransipasien_id])->one();

                // Jika save pendaftaran
                if (!ArrayHelper::getValue($post, 'kunjungan.is_multi_payer') && !isset($post['multi_payer'])) {
                    $pendaftaran_multipayer->delete();
                    $asuransi_multipayer->delete();
                }
            } else {
                $asuransi_multipayer = new AsuransiPasien;
                $pendaftaran_multipayer = new PendaftaranMultipayer;
            }

            /**
            * Update Multipayer Pendaftaran
            * Untuk update ke asuransi_m dan pendaftaran_multipayer_t sudah dihandle trigger di pendaftaran_t  dengan function pendaftaran_t()
            */
            if (ArrayHelper::getValue($post, 'kunjungan.is_multi_payer') && isset($post['multi_payer'])) {
                $asuransi_multipayer->pasien_id = $modelPendaftaran->pasien_id;
                $asuransi_multipayer->penjamin_id = ArrayHelper::getValue($post, 'multi_payer.add_penjamin_id_1');
                $asuransi_multipayer->carabayar_id = ArrayHelper::getValue($post, 'multi_payer.add_carabayar_id_1');
                $asuransi_multipayer->nokartuasuransi = ArrayHelper::getValue($post, 'multi_payer.add_no_asuransi_1');
                $asuransi_multipayer->namapemilikasuransi = ArrayHelper::getValue($post, 'multi_payer.add_namapemilikasuransi_1');
                $asuransi_multipayer->kelastanggunganasuransi_id = ArrayHelper::getValue($post, 'multi_payer.add_kelastanggungan_id_1');
                $asuransi_multipayer->namaperusahaan = ArrayHelper::getValue($post, 'multi_payer.add_namaperusahaan_1');
                $asuransi_multipayer->nomorpokokperusahaan = ArrayHelper::getValue($post, 'multi_payer.add_nomorpokokperusahaan_1');
                $asuransi_multipayer->status_konfirmasi = ArrayHelper::getValue($post, 'multi_payer.add_status_konfirmasi_1');
                $asuransi_multipayer->tgl_konfirmasi = ArrayHelper::getValue($post, 'multi_payer.add_tgl_konfirmasi_1');
                $asuransi_multipayer->save(false);

                $asuransi_multipayer_id = $asuransi_multipayer->getPrimaryKey();

                $pendaftaran_multipayer->asuransipasien_id = $asuransi_multipayer_id;
                $pendaftaran_multipayer->pendaftaran_id = $model->pendaftaran_id;
                $pendaftaran_multipayer->pasien_id = $modelPendaftaran->pasien_id;
                $pendaftaran_multipayer->penjamin_id = ArrayHelper::getValue($post, 'multi_payer.add_penjamin_id_1');
                $pendaftaran_multipayer->carabayar_id = ArrayHelper::getValue($post, 'multi_payer.add_carabayar_id_1');
                $pendaftaran_multipayer->kelastanggunganasuransi_id = ArrayHelper::getValue($post, 'multi_payer.add_kelastanggungan_id_1');
                $pendaftaran_multipayer->is_multipayer = true;
                $pendaftaran_multipayer->save(false);
            }

            $transaction->commit();
            /** edit doco helper callback, untuk kebutuhan bg process edited tindakan
            *   return update success
            **/

            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => DocoMessages::SUC_TITLE,
                'text' => DocoMessages::SUC_MESSAGE_UPDATED,
                'data' => [
                    "is_edited_tindakan" => isset($listEditTindakan) && !empty($listEditTindakan) ? true : false
                 ]
            ], 200);
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e,
            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e->getMessage(),
                'additional' => [
                    'keterangan' => $e->getLine()
                ],
            ]);
        }
    }
    private function replaceNullToString($array, $key, $default = null) {
      return ArrayHelper::getValue($array, $key, $default) == null ? '' : ArrayHelper::getValue($array, $key, $default);
    }
}
