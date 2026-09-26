<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KecanduanForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $formasesmen_code;
    public $pemeriksaan_spesialis;
    public $is_dokumen_eklaim;
    public $asesmenmedis_id;

    public $keluhan_utama;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_sekarang_lainnya;
    public $riwayat_penyakit_dahulu;
    public $riwayat_penyakit_dahulu_lainnya;
    public $riwayat_keturunan_obat;
    public $riwayat_keturunan_obat_lainnya;
    public $riwayat_keturunan_alkohol;
    public $riwayat_keturunan_alkohol_lainnya;
    public $riwayat_keturunan_kekerasan;
    public $riwayat_keturunan_kekerasan_lainnya;
    public $riwayat_keturunan_psikologis;
    public $riwayat_keturunan_psikologis_lainnya;
    public $pengkajian_psikologi;
    public $respon_penerimaan;
    public $pemeriksaan_fisik;
    public $pemeriksaan_fisik_lainnya;
    public $adl_makan_minum;
    public $adl_mandi;
    public $adl_bab;
    public $adl_berpakaian;
    public $adl_istirahat;
    public $adl_obat;
    public $kebutuhan_edukasi;
    public $kebutuhan_edukasi_lainnya;

    public $askep_kesadaran;
    public $askep_gcs;
    public $askep_gcs_e;
    public $askep_gcs_v;
    public $askep_gcs_m;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_tubuh;

    public $diagnosa_keperawatan;
    public $rencana_keperawatan;
    public $pengkajian_sistem;
    public $diagnosa_medis;
    public $rencana_terapi;
    public $saran_dokter;
    public $laboratorium;
    public $radiologi;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [[
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasien_id',
                'pemeriksaan_spesialis',
                'formasesmen_code',
                'is_dokumen_eklaim',
                'asesmenmedis_id',

                'keluhan_utama',
                'riwayat_penyakit_sekarang',
                'riwayat_penyakit_sekarang_lainnya',
                'riwayat_penyakit_dahulu',
                'riwayat_penyakit_dahulu_lainnya',
                'riwayat_keturunan_obat',
                'riwayat_keturunan_obat_lainnya',
                'riwayat_keturunan_alkohol',
                'riwayat_keturunan_alkohol_lainnya',
                'riwayat_keturunan_kekerasan',
                'riwayat_keturunan_kekerasan_lainnya',
                'riwayat_keturunan_psikologis',
                'riwayat_keturunan_psikologis_lainnya',
                'pengkajian_psikologi',
                'respon_penerimaan',
                'pemeriksaan_fisik',
                'pemeriksaan_fisik_lainnya',
                'adl_makan_minum',
                'adl_mandi',
                'adl_bab',
                'adl_berpakaian',
                'adl_istirahat',
                'adl_obat',
                'kebutuhan_edukasi',
                'kebutuhan_edukasi_lainnya',
                'askep_kesadaran',
                'askep_gcs',
                'askep_gcs_e',
                'askep_gcs_v',
                'askep_gcs_m',
                'berat_badan',
                'tinggi_badan',
                'tekanan_darah',
                'frekuensi_nadi',
                'frekuensi_nafas',
                'suhu_tubuh',
                'diagnosa_keperawatan',
                'rencana_keperawatan',
                'pengkajian_sistem',
                'diagnosa_medis',
                'rencana_terapi',
                'saran_dokter',
                'laboratorium',
                'radiologi',
                
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }
}
