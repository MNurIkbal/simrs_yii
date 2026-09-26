<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KebidananForm extends Model
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
    public $riwayat_keluhan;
    public $riwayat_penyakit_dahulu;
    public $riwayat_pengobatan;
    public $riwayat_penyakit_keluarga;
    public $riwayat_alergi;
    public $riwayat_ginekologi;
    public $riwayat_kb;
    
    public $keadaan_umum;
    public $kesadaran;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_badan;
    public $puting_susu;
    public $pengeluaran_asi;
    public $tfu;
    public $kontraksi_uterus;
    public $luka_operasi;
    public $kandung_kemih;
    public $pengeluaran_lochia;
    public $luka_perineum;
    public $lab;
    public $usg;
    public $xray;
    public $diagnosa_kebidanan;
    public $pernah_dirawat;
    public $kapan_dirawat;
    public $dimana_dirawat;
    public $diagnosis_dirawat;

    public $nama_obat;
    public $dosis;
    public $waktu_penggunaan;
    public $riwayat_alergi_lainnya;
    public $riwayat_obs_g;
    public $riwayat_obs_p;
    public $riwayat_obs_a;
    public $riwayat_obs_hpht;
    public $riwayat_obs_tp;
    public $riwayat_obs_kehamilan;
    public $colostrum;
    public $tindakan;
    public $dosis_terapi;
    public $riwayat_penyakit_dahulu_lainnya;

    /**
     * @return array the validation rules.
     */
    public function rules(
    ) {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'formasesmen_code',
                    'pemeriksaan_spesialis',
                    'is_dokumen_eklaim',
                    'asesmenmedis_id',
                    'keluhan_utama',
                    'riwayat_keluhan',
                    'riwayat_penyakit_dahulu',
                    'riwayat_pengobatan',
                    'riwayat_penyakit_keluarga',
                    'riwayat_alergi',
                    'riwayat_ginekologi',
                    'riwayat_kb',
                    'keadaan_umum',
                    'kesadaran',
                    'tekanan_darah',
                    'frekuensi_nadi',
                    'frekuensi_nafas',
                    'suhu_badan',
                    'puting_susu',
                    'pengeluaran_asi',
                    'tfu',
                    'kontraksi_uterus',
                    'luka_operasi',
                    'kandung_kemih',
                    'pengeluaran_lochia',
                    'luka_perineum',
                    'lab',
                    'usg',
                    'xray',
                    'diagnosa_kebidanan',
                    'pernah_dirawat',
                    'kapan_dirawat',
                    'dimana_dirawat',
                    'diagnosis_dirawat',
                    'nama_obat',
                    'dosis',
                    'waktu_penggunaan',
                    'riwayat_alergi_lainnya',
                    'riwayat_obs_g',
                    'riwayat_obs_p',
                    'riwayat_obs_a',
                    'riwayat_obs_hpht',
                    'riwayat_obs_tp',
                    'riwayat_obs_kehamilan',
                    'colostrum',
                    'tindakan',
                    'dosis_terapi',
                    'riwayat_penyakit_dahulu_lainnya'
                ],
                'safe'
            ],
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
