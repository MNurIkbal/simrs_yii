<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class GinekologiForm extends Model
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

    // data subjektif
    public $sumber_data;
    public $sumber_data_lainnya;
    public $keluhan_utama;
    public $umur_menarche;
    public $lama_haid;
    public $jumlah_haid;
    public $haid_terakhir;
    public $perkiraan_partus;
    public $riwayat_mens;
    public $kawin;
    public $umur_kawin;
    public $suami1;
    public $suami2;
    public $riwayat_hamil_g;
    public $riwayat_hamil_p;
    public $riwayat_hamil_a;
    public $hamil_muda;
    public $hamil_tua;
    public $hamil_muda_lainnya;
    public $hamil_tua_lainnya;
    public $tanggal_partus;
    public $tempat_partus;
    public $umur_hamil;
    public $jenis_persalinan;
    public $penolong_persalinan;
    public $penyulit;
    public $bb_anak;
    public $keadaan_anak;
    public $pernah_dirawat;
    public $kapan_dirawat;
    public $dimana_dirawat;
    public $pernah_dioperasi;
    public $kapan_dioperasi;
    public $dimana_dioperasi;
    public $riwayat_penyakit_keluarga;
    public $riwayat_penyakit_keluarga_lainnya;
    public $riwayat_ginekologi;
    public $riwayat_ginekologi_lainnya;
    public $metode_kb;
    public $lama_kb;
    public $komplikasi_kb;
    public $pola_makan;
    public $pola_minum;
    public $pola_tidur;
    public $pola_bab;
    public $pola_bak;
    public $penerimaan_kehamilan;
    public $sosial_support;

    // riwayat alergi
    public $riwayat_alergi;
    public $riwayat_alergi_lainnya;
    public $riwayat_alergi_reaksi;

    // riwayat status psikologis
    public $status_psikologi;
    public $status_psikologi_lainnya;
    public $status_mental;
    public $masalah_lainnya;
    public $perilaku_lainnya;
    public $hubungan_pasien;
    public $nama_kerabat;
    public $hubungan_kerabat;
    public $telepon_kerabat;
    public $kebiasaan_beribadah;
    public $pengambil_keputusan;
    public $pekerjaan;
    public $tempat_tinggal;
    public $tempat_tinggal_lainnya;
    public $kesulitan_komunikasi;
    public $kebiasaan_diet;
    public $kebiasaan_diet_lainnya;
    public $kebiasaan_beribadah_kultural;
    public $kepercayaan;
    public $kepercayaan_lainnya;
    public $status_demografi;
    public $pantangan_berobat;
    public $pantangan_berobat_lainnya;

    // data objektif
    public $kesadaran;
    public $gcs_e;
    public $gcs_m;
    public $gcs_v;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_badan;
    public $berat_badan;
    public $tinggi_badan;
    public $pf_mata;
    public $pf_dada;
    public $pf_ekstremitas;
    public $pf_sistem_kardiopulmonal;
    public $obstetric_inspeksi;
    public $obstetric_inspeksi_lainnya;
    public $obstetric_palpasi;
    public $obstetric_taksiran_janin;
    public $obstetric_auskultasi;
    public $obstetric_auskultasi_teratur;
    public $obstetric_kontraksi;
    public $obstetric_kontraksi_teratur;
    public $ginekologi_genital;
    public $ginekologi_inspeksi;
    public $ginekologi_vaginal;
    public $nifas_tfu;
    public $nifas_kontraksi;
    public $nifas_luka;
    public $nifas_lochea;
    public $lab;
    public $usg;

    // penilaian tingkat nyeri
    public $terdapat_keluhan;
    public $terdapat_keluhan_lainnya;
    public $waktu_nyeri;
    public $frekuensi;
    public $pencetus_nyeri;
    public $pencetus_nyeri_lainnya;
    public $pereda_nyeri;
    public $pereda_nyeri_lainnya;
    public $nyeri_dirasakan;
    public $nyeri_dirasakan_lainnya;
    public $kategori_nyeri;
    public $skor_nyeri;
    public $total_skor_nyeri;

    // penilaian risiko jatuh
    public $riwayat_jatuh;
    public $skor_riwayat_jatuh;
    public $diagnosis_sekunder;
    public $skor_diagnosis_sekunder;
    public $ambulasi;
    public $skor_ambulasi;
    public $terpasang_infus;
    public $skor_terpasang_infus;
    public $gaya_berjalan;
    public $skor_gaya_berjalan;
    public $status_mental_rj;
    public $skor_status_mental_rj;
    public $total_skor_risiko_jatuh;
    public $kriteria_penilaian_hasil;

    // penilaian risiko dekubitus
    public $skor_kondisi_fisik;
    public $skor_status_mental;
    public $skor_aktifitas;
    public $skor_mobilitas;
    public $skor_inkontinensia;
    public $kategori_dekubitus;

    // penilaian status fungsional
    public $skor_defekasi;
    public $skor_berkemih;
    public $skor_bersih;
    public $skor_jamban;
    public $skor_makan;
    public $skor_duduk;
    public $skor_berjalan;
    public $skor_baju;
    public $skor_tangga;
    public $skor_mandi;
    public $kategori_status_fungsional;

    // skrining nutrisi
    public $skrining_asupan_makan;
    public $skrining_metabolisme;
    public $skrining_bb;
    public $skrining_hb;
    public $kategori_skor_nutrisi;

    // kebutuhan edukasi
    public $kebutuhan_pembelajaran;
    public $kebutuhan_pembelajaran_lainnya;

    // skrining faktor risiko
    public $risiko_1;
    public $risiko_2;
    public $risiko_3;
    public $risiko_4;
    public $risiko_5;
    public $risiko_6;
    public $risiko_7;
    public $risiko_8;
    public $risiko_9;
    public $risiko_10;
    public $keterangan_1;
    public $keterangan_2;
    public $keterangan_3;
    public $keterangan_4;
    public $keterangan_5;
    public $keterangan_6;
    public $keterangan_7;
    public $keterangan_8;
    public $keterangan_9;
    public $keterangan_10;
    public $perkiraan_lama_rawat;
    public $perkiraan_tanggal_pulang;
    public $jenis_pemeriksaan;
    public $asal_pemeriksaan;
    public $jumlah_pemeriksaan;

    // identifikasi kebutuhan
    public $privasi_lawan_jenis;
    public $privasi_orang_lain;
    public $privasi_bagian_tubuh;
    public $privasi_informasi;

    // nilai nilai pribadi
    public $nilai_pribadi;
    public $nilai_pribadi_lainnya;
    public $diagnosa_kebidanan;
    public $rencana_kebidanan;
    public $letak_punggung;
    public $presentasi;
    public $checked_risiko;


    /**
     * @return array the validation rules.
     */
    public function rules(
    ) {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'frekuensi_nadi', 'frekuensi_nafas'], 'integer'],
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'formasesmen_code',
                    'pemeriksaan_spesialis',
                    'is_dokumen_eklaim',
                    'asesmenmedis_id',
                    'sumber_data',
                    'sumber_data_lainnya',
                    'keluhan_utama',
                    'umur_menarche',
                    'lama_haid',
                    'jumlah_haid',
                    'haid_terakhir',
                    'perkiraan_partus',
                    'riwayat_mens',
                    'kawin',
                    'umur_kawin',
                    'suami1',
                    'suami2',
                    'riwayat_hamil_g',
                    'riwayat_hamil_p',
                    'riwayat_hamil_a',
                    'hamil_muda',
                    'hamil_tua',
                    'hamil_muda_lainnya',
                    'hamil_tua_lainnya',
                    'tanggal_partus',
                    'tempat_partus',
                    'umur_hamil',
                    'jenis_persalinan',
                    'penolong_persalinan',
                    'penyulit',
                    'bb_anak',
                    'keadaan_anak',
                    'pernah_dirawat',
                    'kapan_dirawat',
                    'dimana_dirawat',
                    'pernah_dioperasi',
                    'kapan_dioperasi',
                    'dimana_dioperasi',
                    'riwayat_penyakit_keluarga',
                    'riwayat_penyakit_keluarga_lainnya',
                    'riwayat_ginekologi',
                    'riwayat_ginekologi_lainnya',
                    'metode_kb',
                    'lama_kb',
                    'komplikasi_kb',
                    'pola_makan',
                    'pola_minum',
                    'pola_tidur',
                    'pola_bab',
                    'pola_bak',
                    'penerimaan_kehamilan',
                    'sosial_support',
                    'riwayat_alergi',
                    'riwayat_alergi_lainnya',
                    'riwayat_alergi_reaksi',
                    'status_psikologi',
                    'status_psikologi_lainnya',
                    'status_mental',
                    'masalah_lainnya',
                    'perilaku_lainnya',
                    'hubungan_pasien',
                    'nama_kerabat',
                    'hubungan_kerabat',
                    'telepon_kerabat',
                    'kebiasaan_beribadah',
                    'pengambil_keputusan',
                    'pekerjaan',
                    'tempat_tinggal',
                    'tempat_tinggal_lainnya',
                    'kesulitan_komunikasi',
                    'kebiasaan_diet',
                    'kebiasaan_diet_lainnya',
                    'kebiasaan_beribadah_kultural',
                    'kepercayaan',
                    'kepercayaan_lainnya',
                    'status_demografi',
                    'pantangan_berobat',
                    'pantangan_berobat_lainnya',
                    'kesadaran',
                    'gcs_e',
                    'gcs_m',
                    'gcs_v',
                    'tekanan_darah',
                    'frekuensi_nadi',
                    'frekuensi_nafas',
                    'suhu_badan',
                    'berat_badan',
                    'tinggi_badan',
                    'pf_mata',
                    'pf_dada',
                    'pf_ekstremitas',
                    'pf_sistem_kardiopulmonal',
                    'obstetric_inspeksi',
                    'obstetric_inspeksi_lainnya',
                    'obstetric_palpasi',
                    'obstetric_taksiran_janin',
                    'obstetric_auskultasi',
                    'obstetric_auskultasi_teratur',
                    'obstetric_kontraksi',
                    'obstetric_kontraksi_teratur',
                    'ginekologi_genital',
                    'ginekologi_inspeksi',
                    'ginekologi_vaginal',
                    'nifas_tfu',
                    'nifas_kontraksi',
                    'nifas_luka',
                    'nifas_lochea',
                    'lab',
                    'usg',
                    'terdapat_keluhan',
                    'terdapat_keluhan_lainnya',
                    'waktu_nyeri',
                    'frekuensi',
                    'pencetus_nyeri',
                    'pencetus_nyeri_lainnya',
                    'pereda_nyeri',
                    'pereda_nyeri_lainnya',
                    'nyeri_dirasakan',
                    'nyeri_dirasakan_lainnya',
                    'kategori_nyeri',
                    'skor_nyeri',
                    'total_skor_nyeri',
                    'riwayat_jatuh',
                    'skor_riwayat_jatuh',
                    'diagnosis_sekunder',
                    'skor_diagnosis_sekunder',
                    'ambulasi',
                    'skor_ambulasi',
                    'terpasang_infus',
                    'skor_terpasang_infus',
                    'gaya_berjalan',
                    'skor_gaya_berjalan',
                    'status_mental_rj',
                    'skor_status_mental_rj',
                    'total_skor_risiko_jatuh',
                    'kriteria_penilaian_hasil',
                    'skor_kondisi_fisik',
                    'skor_status_mental',
                    'skor_aktifitas',
                    'skor_mobilitas',
                    'skor_inkontinensia',
                    'kategori_dekubitus',
                    'skor_defekasi',
                    'skor_berkemih',
                    'skor_bersih',
                    'skor_jamban',
                    'skor_makan',
                    'skor_duduk',
                    'skor_berjalan',
                    'skor_baju',
                    'skor_tangga',
                    'skor_mandi',
                    'kategori_status_fungsional',
                    'skrining_asupan_makan',
                    'skrining_metabolisme',
                    'skrining_bb',
                    'skrining_hb',
                    'kategori_skor_nutrisi',
                    'kebutuhan_pembelajaran',
                    'kebutuhan_pembelajaran_lainnya',
                    'risiko_1',
                    'risiko_2',
                    'risiko_3',
                    'risiko_4',
                    'risiko_5',
                    'risiko_6',
                    'risiko_7',
                    'risiko_8',
                    'risiko_9',
                    'risiko_10',
                    'keterangan_1',
                    'keterangan_2',
                    'keterangan_3',
                    'keterangan_4',
                    'keterangan_5',
                    'keterangan_6',
                    'keterangan_7',
                    'keterangan_8',
                    'keterangan_9',
                    'keterangan_10',
                    'perkiraan_lama_rawat',
                    'perkiraan_tanggal_pulang',
                    'jenis_pemeriksaan',
                    'asal_pemeriksaan',
                    'jumlah_pemeriksaan',
                    'privasi_lawan_jenis',
                    'privasi_orang_lain',
                    'privasi_bagian_tubuh',
                    'privasi_informasi',
                    'nilai_pribadi',
                    'nilai_pribadi_lainnya',
                    'diagnosa_kebidanan',
                    'rencana_kebidanan',
                    'letak_punggung',
                    'presentasi',
                    'checked_risiko'
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
