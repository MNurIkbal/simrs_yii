<?php

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class RiwayatPenyakitFormPrima extends DocoBaseModel
{
    public $pendaftaran_id;
    public $pasien_id;
    public $keluhan;
    
    public $pekerjaan;
    public $lokasi_kerja;
    public $matriks_pemeriksaan;
    public $nama_perusaahan;
    public $tipe_pekerja;
    public $prosedur_pemeriksaan;
    public $prosedur_pemeriksaan_text;

    public $tahun_mulai;
    public $tahun_selesai;
    public $perusahaan;
    public $jabatan;
    public $uraian_singkat;
    public $paparan_tempat_kerja;
    public $paparan_tidak_ada;
    public $paparan_bising;
    public $paparan_kimia;
    public $paparan_radiasi;
    public $paparan_stress;
    public $paparan_ergonomis;
    public $paparan_lainnya;
    public $paparan_secara_singkat;

    public $resiko_confined_space;
    public $resiko_security;
    public $resiko_pengemudi;
    public $resiko_lain;
    public $resiko_operator_berat;
    public $resiko_bekerja_ketinggian;
    public $resiko_fire_brigade;
    public $resiko_tangki_penyelam;
    public $resiko_awak_mobil;
    public $resiko_lain_text;

    public $vital_tekanan_darah;
    public $vital_nadi;
    public $vital_suhu;
    public $vital_respirasi;

    public $keluhan_saat_ini;
    public $keluhan_migrain;
    public $keluhan_migrain_text;
    public $keluhan_epilepsi;
    public $keluhan_epilepsi_text;
    public $keluhan_gangguan_pengelihatan;
    public $keluhan_gangguan_pengelihatan_text;
    public $keluhan_gangguan_pendengaran;
    public $keluhan_masalah_hidung;
    public $keluhan_tbc;
    public $keluhan_pneumonia;
    public $keluhan_asma;
    public $keluhan_gangguang_saluran;
    public $keluhan_hernia;
    public $keluhan_nyeri_dada;
    public $keluhan_penyakit_ginjal;
    public $keluhan_batu_ginjal;
    public $keluhan_penyakit_kulit;
    public $keluhan_riwayat_kecelakaan;
    public $keluhan_riwayat_inap_rs;
    public $keluhan_riwayat_operasi;
    public $keluhan_alergi;
    public $keluhan_demam_reumatik;
    public $keluhan_demam_typhoid;
    public $keluhan_demam_berdarah;
    public $keluhan_malaria;
    public $keluhan_hepatitis;
    public $keluhan_diabetes;
    public $keluhan_nyeri_sendi;
    public $keluhan_nyeri_punggung;
    public $keluhan_varises;
    public $keluhan_kanker;
    public $keluhan_psikiatrik;
    public $keluhan_penyakit_kelamin;
    public $keluhan_masalah_kebidanan;
    public $keluhan_hpht;
    public $keluhan_menarche;
    public $keluhan_keteranganobat;
    public $keluhan_perubahanbb;
    public $keluhan_perubahanbb_type;
    public $keluhan_perubahanbb_kg;
    public $keluhan_perubahanbb_napsumakan;
    public $keluhan_merokok;
    public $keluhan_merokok_jenis;
    public $keluhan_merokok_jumlah;
    public $keluhan_merokok_sejak;
    public $keluhan_alkohol;
    public $keluhan_alkohol_jenis;
    public $keluhan_alkohol_jumlah;
    public $keluhan_alkohol_sejak;
    public $keluhan_lainnya;

    public $keluhan_gangguan_pendengaran_text;
    public $keluhan_masalah_hidung_text;
    public $keluhan_tbc_text;
    public $keluhan_pneumonia_text;
    public $keluhan_asma_text;
    public $keluhan_gangguang_saluran_text;
    public $keluhan_hernia_text;
    public $keluhan_nyeri_dada_text;
    public $keluhan_penyakit_ginjal_text;
    public $keluhan_batu_ginjal_text;
    public $keluhan_penyakit_kulit_text;
    public $keluhan_riwayat_kecelakaan_text;
    public $keluhan_riwayat_inap_rs_text;
    public $keluhan_riwayat_operasi_text;
    public $keluhan_alergi_text;
    public $keluhan_demam_reumatik_text;
    public $keluhan_demam_typhoid_text;
    public $keluhan_demam_berdarah_text;
    public $keluhan_malaria_text;
    public $keluhan_hepatitis_text;
    public $keluhan_diabetes_text;
    public $keluhan_nyeri_sendi_text;
    public $keluhan_nyeri_punggung_text;
    public $keluhan_varises_text;
    public $keluhan_kanker_text;
    public $keluhan_psikiatrik_text;
    public $keluhan_penyakit_kelamin_text;
    public $keluhan_keteranganobat_text;
    public $keluhan_lainnya_text;

    public $tingkat_kebiasaan_olahraga;
    public $jenis_olahraga;
    public $penyakit_jantung_stroke;
    public $penyakit_kanker_tumor;
    public $penyakit_riwayat_saudara;


    protected $xssProtected = [
        'keluhan',
        'riwayat_diderita_catatan',
        'riwayat_alergi_catatan',
        'riwayat_dirawat_rs_catatan',
        'riwayat_operasi_catatan',
        'riwayat_imunisasi_catatan',
        'menstruasi',
        'riwayat_kontrasepsi',
        'riwayat_melahirkan',
        'riwayat_keguguran',
        'sedang_hamil',
        'riwayat_pap_smear',
        'riwayat_penyakit_keluarga',
        'olahraga',
        'obat_rutin',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 'pasien_id', 'keluhan', 'riwayat_diderita', 'riwayat_alergi',
                    'riwayat_diderita_catatan', 'riwayat_alergi_catatan', 'riwayat_dirawat_rs',
                    'riwayat_dirawat_rs_catatan', 'riwayat_operasi', 'riwayat_operasi_catatan',
                    'riwayat_imunisasi', 'riwayat_imunisasi_catatan', 'menstruasi', 'riwayat_kontrasepsi',
                    'riwayat_melahirkan', 'riwayat_keguguran', 'sedang_hamil',
                    'riwayat_penyakit_keluarga', 'riwayat_pap_smear', 'rokok', 'alkohol',
                    'kopi', 'olahraga', 'diet', 'tidur', 'obat_rutin',
                    'pekerjaan',
                    'lokasi_kerja',
                    'matriks_pemeriksaan',
                    'nama_perusaahan',
                    'tipe_pekerja',
                    'prosedur_pemeriksaan',
                    'prosedur_pemeriksaan_text',
                    'tahun_mulai',
                    'tahun_selesai',
                    'perusahaan',
                    'jabatan',
                    'uraian_singkat',
                    'paparan_tempat_kerja',
                    'paparan_tidak_ada',
                    'paparan_bising',
                    'paparan_kimia',
                    'paparan_radiasi',
                    'paparan_stress',
                    'paparan_ergonomis',
                    'paparan_lainnya',
                    'paparan_secara_singkat',
                    'resiko_confined_space',
                    'resiko_security',
                    'resiko_pengemudi',
                    'resiko_lain',
                    'resiko_operator_berat',
                    'resiko_bekerja_ketinggian',
                    'resiko_fire_brigade',
                    'resiko_tangki_penyelam',
                    'resiko_awak_mobil',
                    'resiko_lain_text',
                    'vital_tekanan_darah',
                    'vital_nadi',
                    'vital_suhu',
                    'vital_respirasi',
                    'keluhan_saat_ini',
                    'keluhan_migrain',
                    'keluhan_migrain_text',
                    'keluhan_epilepsi',
                    'keluhan_epilepsi_text',
                    'keluhan_gangguan_pengelihatan',
                    'keluhan_gangguan_pengelihatan_text',
                    'keluhan_gangguan_pendengaran',
                    'keluhan_masalah_hidung',
                    'keluhan_tbc',
                    'keluhan_pneumonia',
                    'keluhan_asma',
                    'keluhan_gangguang_saluran',
                    'keluhan_hernia',
                    'keluhan_nyeri_dada',
                    'keluhan_penyakit_ginjal',
                    'keluhan_batu_ginjal',
                    'keluhan_penyakit_kulit',
                    'keluhan_riwayat_kecelakaan',
                    'keluhan_riwayat_inap_rs',
                    'keluhan_riwayat_operasi',
                    'keluhan_alergi',
                    'keluhan_demam_reumatik',
                    'keluhan_demam_typhoid',
                    'keluhan_demam_berdarah',
                    'keluhan_malaria',
                    'keluhan_hepatitis',
                    'keluhan_diabetes',
                    'keluhan_nyeri_sendi',
                    'keluhan_nyeri_punggung',
                    'keluhan_varises',
                    'keluhan_kanker',
                    'keluhan_psikiatrik',
                    'keluhan_penyakit_kelamin',
                    'keluhan_masalah_kebidanan',
                    'keluhan_hpht',
                    'keluhan_menarche',
                    'keluhan_keteranganobat',
                    'keluhan_perubahanbb',
                    'keluhan_perubahanbb_type',
                    'keluhan_perubahanbb_kg',
                    'keluhan_perubahanbb_napsumakan',
                    'keluhan_merokok',
                    'keluhan_merokok_jenis',
                    'keluhan_merokok_jumlah',
                    'keluhan_merokok_sejak',
                    'keluhan_alkohol',
                    'keluhan_alkohol_jenis',
                    'keluhan_alkohol_jumlah',
                    'keluhan_alkohol_sejak',
                    'keluhan_gangguan_pendengaran_text',
                    'keluhan_masalah_hidung_text',
                    'keluhan_tbc_text',
                    'keluhan_pneumonia_text',
                    'keluhan_asma_text',
                    'keluhan_gangguang_saluran_text',
                    'keluhan_hernia_text',
                    'keluhan_nyeri_dada_text',
                    'keluhan_penyakit_ginjal_text',
                    'keluhan_batu_ginjal_text',
                    'keluhan_penyakit_kulit_text',
                    'keluhan_riwayat_kecelakaan_text',
                    'keluhan_riwayat_inap_rs_text',
                    'keluhan_riwayat_operasi_text',
                    'keluhan_alergi_text',
                    'keluhan_demam_reumatik_text',
                    'keluhan_demam_typhoid_text',
                    'keluhan_demam_berdarah_text',
                    'keluhan_malaria_text',
                    'keluhan_hepatitis_text',
                    'keluhan_diabetes_text',
                    'keluhan_nyeri_sendi_text',
                    'keluhan_nyeri_punggung_text',
                    'keluhan_varises_text',
                    'keluhan_kanker_text',
                    'keluhan_psikiatrik_text',
                    'keluhan_penyakit_kelamin_text',
                    'keluhan_keteranganobat_text',
                    'keluhan_lainnya',
                    'keluhan_lainnya_text',
                    'tingkat_kebiasaan_olahraga',
                    'jenis_olahraga',
                    'penyakit_jantung_stroke',
                    'penyakit_kanker_tumor',
                    'penyakit_riwayat_saudara',
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
            'keluhan' => Yii::t('fe', 'Keluhan saat ini'),
            'riwayat_diderita' => Yii::t('fe', 'Riwayat penyakit yang pernah di derita'),
            'riwayat_dirawat_rs' => Yii::t('fe', 'Riwayat dirawat di RS'),
            'riwayat_pap_smear' => Yii::t('fe', "Riwayat Pap's Smear"),
            'olahraga' => Yii::t('fe', 'Olah Raga'),
            'pekerjaan' => Yii::t('fe', 'Pekerjaan/Jabatan'),
        ];
    }
}
