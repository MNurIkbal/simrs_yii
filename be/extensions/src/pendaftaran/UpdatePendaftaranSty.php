<?php

/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\pendaftaran;

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
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoKunjunganRd;
use app\modules\v1\models\InfoPasienMcuView;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyPasienMasukPenunjangView;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\SyncEditsantoyusupR;
use Doco\models\KonfigSystem;

use app\modules\v1\payload\EditPendaftaranForm;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\PenanggungBiayaForm;

use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;

class UpdatePendaftaranSty extends \Doco\components\DocoBaseProcessExtension
{
    protected $komponenTotal;

    const IGD = 'igd';
    const RANAP = 'ranap';
    const RAJAL = 'rajal';
    const MCU = 'mcu';
    const ST_YUSUP = 'st-yusup';
    const PENUNJANG = 'penunjang';
    const PAKET = 'paket';
    const TINDAKAN = 'tindakan';

    protected function buildPayload($value, $model)
    {
        return [
            'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
            'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
            'dokter_id' => !empty($model->pegawai_id) ? $model->pegawai_id : $value['dokterpenanggungjawab_id'],
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
            'kelaspelayanan_id' => $value['kelaspelayanan_id'],
            'is_penatajasa' => $value['is_penatajasa'],
            'penjamin_id' => $model->penjamin_id
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

    private function syncUpdatePendaftaran($idPendaftaran)
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

            $penunjang = SyPasienMasukPenunjangView::find()
            ->where(['pendaftaran_id' => $idPendaftaran])
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
            'penunjang' => $penunjang,
            'penanggungJawab' => $penanggungJawab,
        ];

        return $result;
    }

    private function setLog($idPendaftaran, $result, $sendData)
    {
        $modelSync = new SyncEditsantoyusupR;
        $modelSync->pendaftaran_id = $idPendaftaran;
        $modelSync->pasien_id = null;
        $modelSync->created_date = date("Y-m-d H:i:s");
        $modelSync->count_sync = 1;
        $modelSync->payload = json_encode($result);
        $modelSync->state = 'edit';
        $modelSync->save(false);
    }

	protected function processFlow()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();

        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $transaction = $connection->beginTransaction();
        $errorParse = $dokterkonsul = [];

        try {
            $model = new EditPendaftaranForm;
            $modelAsuransi = new AsuransiForm;
            $modelPenanggungBiaya = new PenanggungBiayaForm;
            $modelBpjs = new Bpjs;
            $detailTindakan = array();
            $detailTindakanPenunjang = array();
            $detailTindakanKamar = array();
            $param_rs = isset($post['param_rs']) ? $post['param_rs'] : null;
            $model->attributes = isset($post['kunjungan']) ? $post['kunjungan'] : [];
            //$this->komponenTotal = $this->constans->actionGetId('komponen_total');
            // $model->scenario = ($model->jenis != self::RANAP) ? EditPendaftaranForm::RAJAL_EDIT : 'default';
            switch($model->jenis){
                case self::RANAP:
                    $model->scenario = EditPendaftaranForm::ST_YUSUP_RANAP;
                    break;
                case self::PENUNJANG:
                    $model->scenario = 'st-yusup-penunjang';
                    break;
                default:
                    $model->scenario = EditPendaftaranForm::ST_YUSUP;
                    break;
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
                    ])->where([
                        'pasienadmisi_id' => $model->pasienadmisi_id
                    ])->asArray()->one();

                    /** Untuk Update Catatan  */
                    $pendaftaran = Pendaftaran::find()->where([
                        'pendaftaran_id' => $model->pendaftaran_id
                    ])->one();

                    $pendaftaran->keterangan_pendaftaran = $model->keterangan;
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

                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                            'text' => DocoMessages::ERR_MESSAGE_DIPULANGKAN
                        ]);
                    }

                    $isTitipan = false;
                    $kelasPerawatan = $modelPendaftaran->kelaspelayanan_id;

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
                    $modelPendaftaran->hakkelas_id = $model->hakkelas_id;
                    $modelPendaftaran->kelaspermintaan_id = $model->kelaspermintaan_id;
                    $modelPendaftaran->dokterpengirim_id = $model->dokterpengirim_id;
                    $modelPendaftaran->prosedurmasuk_id = $model->prosedurmasuk_id;
                    $modelPendaftaran->diagnosa_awal = $model->diagnosa_awal;

                    if (!empty($model->dokterkonsul_id)) {
                        foreach ($model->dokterkonsul_id as $key => $value) {
                            if(!empty($value)) {
                                $dokterkonsul[] = $value;
                            } else {
                                break;
                            }
                        }
                    }
                    $modelPendaftaran->dokterkonsul_id = !empty($dokterkonsul) ? json_encode($dokterkonsul) : null;
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
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->asArray()->one();
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
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->asArray()->one();

                        $modelPenunjang = PasienMasukPenunjang::find()
                        ->where(['pendaftaran_id' => $model->pendaftaran_id])
                        ->one();
                        $modelPenunjang->pegawai_id = $model->pegawai_id;
                        $modelPenunjang->jeniskasuspenyakit_id = $model->jeniskasuspenyakit_id;
                        $modelPenunjang->ruanganasal_id = $model->ruangan_id;
                        $modelPenunjang->ruangan_id = $model->ruangan_id;
                        $modelPenunjang->save();
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
                        ])->where([
                            'pendaftaran_id' => $model->pendaftaran_id
                        ])->asArray()->one();
                    }

                    $statusPulang = isset($dataPendaftaran['status_periksa']) ? $dataPendaftaran['status_periksa'] : null;

                    if ($statusPulang === DocoConstants::STATUS_PERIKSA_PULANG 
                            || $statusPulang === DocoConstants::STATUS_PERIKSA_BTL_PERIKSA
                            || $statusPulang === DocoConstants::STATUS_PERIKSA_BTL_KUNJ) {

                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                            'text' => DocoMessages::ERR_MESSAGE_DIPULANGKAN
                        ]);
                    }

                    $modelPendaftaran = Pendaftaran::find()->where([
                        'pendaftaran_id' => $model->pendaftaran_id
                    ])->one();

                    $tgl_pendaftaran = !empty($model->tgl_pendaftaran) ? date('Y-m-d H:i:s', strtotime($model->tgl_pendaftaran)) : $modelPendaftaran->tgl_pendaftaran;

                    $modelPendaftaran->penjamin_id = $model->penjamin_id;
                    $modelPendaftaran->tgl_pendaftaran = $tgl_pendaftaran;
                    $modelPendaftaran->carabayar_id = $model->carabayar_id;
                    $modelPendaftaran->ruangan_id = (!empty($model->ruangan_id)) ? $model->ruangan_id : $dataPendaftaran['ruangan_id'];
                    // $modelPendaftaran->pegawai_id = $model->pegawai_id;
                    $modelPendaftaran->dokterpengganti_id = $model->dokterpengganti_id;
                    $modelPendaftaran->jeniskasuspenyakit_id = $model->jeniskasuspenyakit_id;
                    $modelPendaftaran->keterangan_pendaftaran = $model->keterangan;
                    $modelPendaftaran->limit_tagihan = $model->limit_tagihan;
                    $kelasPerawatan = $dataPendaftaran['kelaspelayanan_id'];

                    if ($model->jenis === self::PENUNJANG) {
                        $modelPendaftaran->styrujukaninstalasi_id = $model->styrujukaninstalasi_id;
                        $modelPendaftaran->dokterpengirim_id = $model->dokterpengirim_id;
                        // $modelPendaftaran->instalasi_id = $model->instalasiasal_id;
                    }

                    $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                    $petugas_id = $jwt->loginpemakai_id;
                    $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                    $modelPendaftaran->petugas_id = $petugas_id;
                    $modelPendaftaran->petugas_tgl_pembuat = $petugas_tgl_pembuat;

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

            $changeTindakan = false;
            $noRegist = isset($dataPendaftaran['no_pendaftaran']) ? $dataPendaftaran['no_pendaftaran'] : null;

            if ($modelPendaftaran->pegawai_id != $model->pegawai_id) {
                $modelPendaftaran->pegawai_id = $model->pegawai_id;
                $changeTindakan = true;
            }

            /** Check Kondisi perubahan Cara bayar */
            if ((int) $dataPendaftaran['penjamin_id'] !== (int) $model->penjamin_id) {
                $changeTindakan = true;
                switch ($model->group_carabayar) {
                    case DocoConstants::GROUP_BPJS :
                        $namaPasien = !empty($dataPendaftaran['nama_pasien']) ? $dataPendaftaran['nama_pasien'] : null;
                        if (empty($model->nosep)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_SEP_REQUIRED
                            ]);
                        }

                        $qBpjs = Bpjs::find()->select([
                            'bpjs_id'
                        ])->where([
                            'nosep' => $model->nosep
                        ])->asArray()->one();

                        if (!empty($qBpjs)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                'text' => DocoMessages::ERR_MESSAGE_SEP_EXIST
                            ]);
                        }
                        /** Save to cache for respose find SEP */
                        $findSep = $cache->get($model->nosep);

                        if (empty($findSep)) {
                            $findSep = (new Bpjs)->referensiCariSep($model->nosep);
                            $cache->set($model->nosep, $findSep);
                        }


                        if (isset($findSep['metaData']['code'])) {
                            if ($findSep['metaData']['code'] == 200) {
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
                                    $messages .= "Apakah Anda yakin akan melanjutkan proses?";
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
                        if(!empty($modelPendaftaran->bpjs_id)) {
                            $bpjs = Bpjs::findOne($modelPendaftaran->bpjs_id);
                            $noSep = !empty($bpjs->nosep) ? $bpjs->nosep : '';
                            if($bpjs && $modelPendaftaran->bpjs_id) {
                                if($bpjs->delete($modelPendaftaran->bpjs_id)) {
                                    $dataBpjs = [
                                        'noSep' => $noSep,
                                        'user' => Yii::$app->jwt->user->nama_pemakai
                                    ];
                                    $this->deleteSep($dataBpjs);
                                }
                            }
                        }

                        $modelPendaftaran->bpjs_id = null;
                        break;
                    default:
                        /** Sebagai Umum */
                        $modelPendaftaran->bpjs_id = null;
                        $this->deleteDataAsuransi($modelPendaftaran->asuransipasien_id, $model->pendaftaran_id);

                        break;
                }
            }

            if(!empty($post['asuransi'])) {
                $asuransipasien_id = $modelAsuransi->asuransipasien_id;
                $asuransi = (!empty($asuransipasien_id)) ? 
                    AsuransiPasien::findOne($asuransipasien_id) : new AsuransiPasien;

                $masaberlakukartu = !empty($modelAsuransi->masaberlakukartu) ? date('Y-m-d', strtotime($modelAsuransi->masaberlakukartu)) : date('Y-m-d');

                $asuransi->attributes = $modelAsuransi->attributes;
                $asuransi->kelastanggunganasuransi_id = $modelAsuransi->kelastanggungan_id;
                $asuransi->pasien_id = $modelPendaftaran->pasien_id;
                $asuransi->penjamin_id = $model->penjamin_id;
                $asuransi->carabayar_id = $model->carabayar_id;
                $asuransi->pendaftaran_id = $model->pendaftaran_id;
                $asuransi->masaberlakukartu = $masaberlakukartu;

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

                $riwayatTindakan = TindakanPelayanan::find()
                ->andWhere([
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $idAdmisi,
                    'tindakansudahbayar_id' => null
                ])
                ->asArray()
                ->all();

                if (!empty($riwayatTindakan)) {
                    foreach ($riwayatTindakan as $key => $value) {
                        $listEditTindakan[] = $this->buildPayload($value,$model);
                    }
                }
            }

            $modelPendaftaran->save();

            if (array_key_exists('nomor_urut', $post['kunjungan']) && $post['kunjungan']['nomor_urut']) {
                $antrian = Antrian::find()
                ->where([
                    'pendaftaran_id' => $modelPendaftaran->pendaftaran_id,
                    'jenisantrian_id' => DocoConstants::VAR_JA_P
                ])
                ->one();

                if ($antrian) {
                    $antrian->no_antrian = $post['kunjungan']['nomor_urut'];
                    $antrian->pegawai_id = $modelPendaftaran->pegawai_id;
                    $antrian->save();
                }
            }

            $transaction->commit();

            if (!empty($listEditTindakan)) {
                (new KasirService)->editTindakan(
                    $noRegist,
                    $listEditTindakan
                );
            }

            switch($model->jenis){
                case self::IGD:
                    $route = 'app/update-pendaftaran-igd';
                    break;
                case self::RANAP:
                    $route = 'app/update-pendaftaran-ranap';
                    break;
                case self::PENUNJANG:
                    $route = 'app/update-pendaftaran-penunjang';
                    break;
                default:
                    $route = 'app/update-pendaftaran-rajal';
                    break;
            }

            $params['route'] = $route;
            $params['data'] = $this->syncUpdatePendaftaran($model->pendaftaran_id);
            $sendData = (new PendaftaranService)->syncPendaftaranSty($params, function($data, $result) {
                return $result;
            });
            $payload = $params['data'];
            $this->setLog($model->pendaftaran_id, $payload, $sendData);
            
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
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
}