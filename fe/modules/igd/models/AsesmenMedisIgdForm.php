<?php

namespace app\modules\igd\models;

use Yii;

class AsesmenMedisIgdForm extends \yii\base\Model
{
    // Asesmen Medis
    public $asesmenmedisrd_id;
    public $pendaftaran_id;
    public $tgl_pasien_datang;
    public $tgl_asesmen;
    public $triage;
    public $dikirim_oleh;
    public $kasus_polisi;
    public $cara_datang;
    public $cara_datang_diantar;
    public $kasus_kecelakaan;

    public $asesmen_allo;
    public $asesmen_auto;
    public $asesmen_allo_anamnesa;
    public $asesmen_auto_anamnesa;
    public $keluhan_utama;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_dahulu;
    public $riwayat_terapi_sebelumnya;
    public $alergi;

    // Pemeriksaan Fisik
    public $berat_badan;
    public $tinggi_badan;
    public $imt;
    public $bb_ideal;
    public $imt_kategori;

    public $tekanandarah;
    public $nadi;
    public $pernapasan;
    public $suhu;
    public $saturasi_o2;
    public $skrining_nyeri;
    public $pilih_skala;
    public $skala_nyeri;
    public $skala_nyeri_anak;
    public $keadaan_umum;
    public $kesadaran;

    public $gcseye_id;
    public $gcsverbal_id;
    public $gcsmotorik_id;
    public $hasil_gcs;
    public $is_kapitis;
    public $keterangan_gcs;

    // Pemeriksaan Penunjang
    public $laboratorium;
    public $radiologi;
    public $ekg;
    public $lain_lain;

    public $diagnosa_id;
    public $terapi;

    // Tindak Lanjut
    public $tindak_lanjut;

    // Kondisi Saat Meninggalkan IGD
    public $ku_keluar;
    public $tekanandarah_keluar;
    public $nadi_keluar;
    public $pernapasan_keluar;
    public $saturasi_o2_keluar;
    public $suhu_keluar;

    // Secondary Survey
    public $survey_kepala;
    public $survey_kepala_lacerasi;
    public $survey_kepala_battle_sign;
    public $survey_kepala_lainnya;
    public $survey_mata;
    public $survey_mata_lainnya; //
    public $survey_mulut;
    public $survey_mulut_luka_dalam;
    public $survey_mulut_lainnya;
    public $survey_telinga;
    public $survey_telinga_lainnya; //
    public $survey_leher;
    public $survey_leher_lainnya;
    public $survey_extremitas;
    public $survey_extremitas_pulsasi;
    public $survey_extremitas_pulsasi_lainnya;
    public $survey_dada;
    public $survey_dada_simetris_asimetris;
    public $survey_dada_pneumo_hamatotoraks;
    public $survey_dada_nyeri_lokasi;
    public $survey_dada_nyeri_kapan;
    public $survey_dada_nyeri_durasi;
    public $survey_dada_nyeri_kegiatan;
    public $survey_dada_bunyi_jantung;
    public $survey_abdomen;
    public $survey_abdomen_memas;
    public $survey_abdomen_nyeri;
    public $survey_abdomen_lainnya;
    public $survey_abdomen_bising_usus;
    public $survey_pelvis;
    public $survey_pelvis_lainnya;
    public $survey_medulla_spinalis;
    public $survey_kolumna_vertebralis;
    public $survey_kepala_utuh;
    public $extremitas_utuh;
    public $extremitas_fraktur;
    public $extremitas_nyeri;
    public $extremitas_deformitas;
    public $extremitas_defisit_neurologis;
    public $extremitas_jejas;
    public $extremitas_pulsasi;
    public $extremitas_lainnya;
    public $survey_kepala_tidak_ada_kelainan;
    public $survey_mulut_tidak_ada_kelainan;
    public $survey_dada_tidak_ada_kelainan;
    public $survey_abdomen_tidak_ada_kelainan;
    public $survey_thoraks;
    public $survey_thoraks_lainnya;
    
    public $kepala;
    public $mulut;
    public $mata;
    public $tht;
    public $leher;
    public $toraks;
    public $jantung;
    public $paru;
    public $abdomen;
    public $pergerakan;
    public $perkusi;
    public $nafas;
    public $rochi;
    public $wheezing;
    
    public $irama;
    public $bunyi_jantung;
    public $kelainan;
    public $benjolan;
    public $nyeri_tekan;
    public $hernia;
    public $bising_usus;
    public $distensi;
    public $tulang_belakang;
    public $sistem_saraf;
    public $genetalia;
    public $edema;
    public $crt;
    public $kepala_lainnya;
    public $mata_lainnya;
    public $tht_lainnya;
    public $leher_lainnya;
    public $mulut_lainnya;
    public $thoraks_lainnya;
    public $perkusi_lainnya;
    public $pernapasan_lainnya;
    public $bunyi_jantung_lainnya;
    public $kelaianan_lainnya;
    public $benjolan_lainnya;
    public $nyeri_tekan_lainnya;
    public $hernia_lainnya;
    public $bising_usus_lainnya;
    public $distensi_lainnya;
    public $tulang_belakang_lainnya;
    public $sistem_saraf_lainnya;
    public $genetalia_lainnya;
    public $edema_lainnya;
    public $crt_lainnya;
    public $pemeriksaan_fisik_lainnya;
    public $additional_data;
    public $diagnosis;
    public $allo_or_auto;
    public $periksatubuh;

    public function rules()
    {
        return [
            [
                [
                    'asesmenmedisrd_id',
                    'pendaftaran_id',
                    'tgl_pasien_datang',
                    'tgl_asesmen',
                    'triage',
                    'dikirim_oleh',
                    'kasus_polisi',
                    'cara_datang',
                    'cara_datang_diantar',
                    'asesmen_auto',
                    'asesmen_allo',
                    'asesmen_allo_anamnesa',
                    'asesmen_auto_anamnesa',
                    'keluhan_utama',
                    'riwayat_penyakit_sekarang',
                    'riwayat_penyakit_dahulu',
                    'riwayat_terapi_sebelumnya',
                    'alergi',
                    'berat_badan',
                    'tinggi_badan',
                    'imt',
                    'bb_ideal',
                    'imt_kategori',
                    'tekanandarah',
                    'nadi',
                    'pernapasan',
                    'suhu',
                    'saturasi_o2',
                    'skrining_nyeri',
                    'pilih_skala',
                    'skala_nyeri',
                    'skala_nyeri_anak',
                    'keadaan_umum',
                    'kesadaran',
                    'gcseye_id',
                    'gcsverbal_id',
                    'gcsmotorik_id',
                    'hasil_gcs',
                    'is_kapitis',
                    'keterangan_gcs',
                    'laboratorium',
                    'radiologi',
                    'ekg',
                    'lain_lain',
                    'diagnosa_id',
                    'terapi',
                    'tindak_lanjut',
                    'ku_keluar',
                    'tekanandarah_keluar',
                    'nadi_keluar',
                    'pernapasan_keluar',
                    'saturasi_o2_keluar',
                    'suhu_keluar',
                    'kasus_kecelakaan',
                    'survey_kepala',
                    'survey_kepala_lacerasi',
                    'survey_kepala_battle_sign',
                    'survey_kepala_lainnya',
                    'survey_mata',
                    'survey_mulut',
                    'survey_mulut_luka_dalam',
                    'survey_mulut_lainnya',
                    'survey_telinga',
                    'survey_leher',
                    'survey_leher_lainnya',
                    'survey_extremitas',
                    'survey_extremitas_pulsasi',
                    'survey_dada',
                    'survey_dada_simetris_asimetris',
                    'survey_dada_pneumo_hamatotoraks',
                    'survey_dada_nyeri_lokasi',
                    'survey_dada_nyeri_kapan',
                    'survey_dada_nyeri_durasi',
                    'survey_dada_nyeri_kegiatan',
                    'survey_dada_bunyi_jantung',
                    'survey_abdomen',
                    'survey_abdomen_memas',
                    'survey_abdomen_nyeri',
                    'survey_abdomen_lainnya',
                    'survey_abdomen_bising_usus',
                    'survey_pelvis',
                    'survey_pelvis_lainnya',
                    'survey_medulla_spinalis',
                    'survey_kolumna_vertebralis',
                    'survey_kepala_utuh',
                    'extremitas_utuh',
                    'extremitas_fraktur',
                    'extremitas_nyeri',
                    'extremitas_deformitas',
                    'extremitas_defisit_neurologis',
                    'extremitas_jejas',
                    'extremitas_pulsasi',
                    'survey_kepala_tidak_ada_kelainan',
                    'survey_mulut_tidak_ada_kelainan',
                    'survey_dada_tidak_ada_kelainan',
                    'survey_abdomen_tidak_ada_kelainan',
                    'additional_data',
                    'diagnosis',

                    'kepala',
                    'mulut',
                    'mata',
                    'tht',
                    'leher',
                    'toraks',
                    'pergerakan',
                    'perkusi',
                    'nafas',
                    'rochi',
                    'wheezing',
                    'irama',
                    'bunyi_jantung',
                    'kelainan',
                    'benjolan',
                    'nyeri_tekan',
                    'hernia',
                    'bising_usus',
                    'distensi',
                    'tulang_belakang',
                    'sistem_saraf',
                    'genetalia',
                    'edema',
                    'crt',
                    'kepala_lainnya',
                    'mata_lainnya',
                    'tht_lainnya',
                    'leher_lainnya',
                    'mulut_lainnya',
                    'thoraks_lainnya',
                    'perkusi_lainnya',
                    'pernapasan_lainnya',
                    'bunyi_jantung_lainnya',
                    'kelaianan_lainnya',
                    'benjolan_lainnya',
                    'nyeri_tekan_lainnya',
                    'hernia_lainnya',
                    'bising_usus_lainnya',
                    'distensi_lainnya',
                    'tulang_belakang_lainnya',
                    'sistem_saraf_lainnya',
                    'genetalia_lainnya',
                    'edema_lainnya',
                    'crt_lainnya',
                    'pemeriksaan_fisik_lainnya',
                    'allo_or_auto'
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

            'tgl_pasien_datang' => 'Tanggal/Jam Pasien Datang',
            'tgl_asesmen' => 'Tanggal Asesmen Dokter',
            'triage' => 'Triage',
            'dikirim_oleh' => 'Dikirim Oleh',
            'kasus_polisi' => 'Kasus Polisi',
            'cara_datang' => '',
            'cara_datang_diantar' => '',
            "asesmen_auto" => '',
            "asesmen_allo" => '',
            'asesmen_allo_anamnesa' => '',
            'asesmen_auto_anamnesa' => '',
            'keluhan_utama' => 'Keluhan Utama',
            'riwayat_penyakit_sekarang' => 'Riwayat Penyakit Sekarang',
            'riwayat_penyakit_dahulu' => 'Riwayat Penyakit Dahulu/Operasi',
            'riwayat_terapi_sebelumnya' => 'Riwayat Terapi Sebelumnya',
            'alergi' => 'Alergi',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'imt' => 'IMT',
            'bb_ideal' => 'Berat Badan Ideal',
            'imt_kategori' => 'Klasifikasi Berat Badan',
            'tekanandarah' => 'Tekanan Darah',
            'nadi' => 'Frekuensi Nadi',
            'pernapasan' => 'Frekuensi Pernafasan',
            'suhu' => 'Suhu',
            'saturasi_o2' => 'Saturasi O2 ',
            'skrining_nyeri' => 'Skrining Nyeri',
            'pilih_skala' => 'Pilih Skala',
            'skala_nyeri' => 'Skala Nyeri',
            'skala_nyeri_anak' => 'Skala Nyeri Anak',
            'keadaan_umum' => 'Keadaan Umum',
            'kesadaran' => 'Kesadaran',
            'gcseye_id' => 'GCS Eye',
            'gcsverbal_id' => 'GCS Verbal',
            'gcsmotorik_id' => 'GCS Motorik',
            'hasil_gcs' => 'Hasil Metode GCS',
            'is_kapitis' => 'Kapitis',
            'keterangan_gcs' => 'Keterangan GCS',
            'laboratorium' => 'Laboratorium',
            'radiologi' => 'Radiologi',
            'ekg' => 'EKG',
            'lain_lain' => 'Lain-Lain',
            'diagnosa_id' => 'Diagnosa',
            'terapi' => 'Terapi',
            'tindak_lanjut' => 'Tindak Lanjut',
            'ku_keluar' => 'KU',
            'tekanandarah_keluar' => 'Tekanan Darah',
            'nadi_keluar' => 'Nadi',
            'pernapasan_keluar' => 'Pernapasan',
            'saturasi_o2_keluar' => 'Saturasi O2',
            'suhu_keluar' => 'Suhu',
            'kasus_kecelakaan' => 'Kasus Kecelakaan',
            'survey_kepala' => 'survey_kepala',
            'survey_kepala_lacerasi' => 'survey_kepala_lacerasi',
            'survey_kepala_battle_sign' => 'survey_kepala_battle_sign',
            'survey_kepala_lainnya' => 'survey_kepala_lainnya',
            'survey_mata' => 'survey_mata',
            'survey_mulut' => 'survey_mulut',
            'survey_mulut_luka_dalam' => 'survey_mulut_luka_dalam',
            'survey_mulut_lainnya' => 'survey_mulut_lainnya',
            'survey_telinga' => 'survey_telinga',
            'survey_leher' => 'survey_leher',
            'survey_leher_lainnya' => 'survey_leher_lainnya',
            'survey_extremitas' => 'survey_extremitas',
            'survey_extremitas_pulsasi' => 'survey_extremitas_pulsasi',
            'survey_dada' => 'survey_dada',
            'survey_dada_simetris_asimetris' => 'survey_dada_simetris_asimetris',
            'survey_dada_pneumo_hamatotoraks' => 'survey_dada_pneumo_hamatotoraks',
            'survey_dada_nyeri_lokasi' => 'Lokasi',
            'survey_dada_nyeri_kapan' => 'Kapan',
            'survey_dada_nyeri_durasi' => 'Durasi',
            'survey_dada_nyeri_kegiatan' => 'Sedang melakukan kegiatan',
            'survey_dada_bunyi_jantung' => 'survey_dada_bunyi_jantung',
            'survey_abdomen' => 'survey_abdomen',
            'survey_abdomen_memas' => 'survey_abdomen_memas',
            'survey_abdomen_nyeri' => 'survey_abdomen_nyeri',
            'survey_abdomen_lainnya' => 'survey_abdomen_lainnya',
            'survey_abdomen_bising_usus' => 'survey_abdomen_bising_usus',
            'survey_pelvis' => 'survey_pelvis',
            'survey_pelvis_lainnya' => 'survey_pelvis_lainnya',
            'survey_medulla_spinalis' => 'survey_medulla_spinalis',
            'survey_kolumna_vertebralis' => 'survey_kolumna_vertebralis',
            'survey_kepala_utuh' => 'survey_kepala_utuh',
            'pemeriksaan_fisik_lainnya' => 'Lain - lain',
            'additional_data' => 'Additional Data',
            'diagnosis' => 'Diagnosis',
            'allo_or_auto' => 'allo_or_auto'
        ];
    }
}
