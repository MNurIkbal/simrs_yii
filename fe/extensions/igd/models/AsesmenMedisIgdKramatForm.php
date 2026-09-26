<?php

namespace app\extensions\igd\models;

use Yii;

class AsesmenMedisIgdKramatForm extends \yii\base\Model
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

    public $diagnosa_primary;
    public $diagnosa_secondary;
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
    public $objective;

    public $additional_data;

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
                    'diagnosa_primary',
                    'diagnosa_secondary',
                    'terapi',
                    'tindak_lanjut',
                    'ku_keluar',
                    'tekanandarah_keluar',
                    'nadi_keluar',
                    'pernapasan_keluar',
                    'saturasi_o2_keluar',
                    'suhu_keluar',
                    'kasus_kecelakaan',
                    'objective',
                    'additional_data',
                ],
                'safe'
            ],
            [
              ['keluhan_utama'],
              'required',
              'message' => '{attribute} tidak boleh kosong.',
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

            'tgl_pasien_datang' => 'Tanggal & Jam Pengkajian',
            'tgl_asesmen' => 'Tanggal Asesmen Dokter',
            'triage' => 'Triase',
            'dikirim_oleh' => 'Dikirim Oleh',
            'kasus_polisi' => 'Kasus Polisi',
            'cara_datang' => 'Cara Pasien Datang',
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
            'diagnosa_primary' => 'Diagnosa Primer',
            'diagnosa_secondary' => 'Diagnosa Sekunder',
            'terapi' => 'Terapi',
            'tindak_lanjut' => 'Tindak Lanjut',
            'ku_keluar' => 'KU',
            'tekanandarah_keluar' => 'Tekanan Darah',
            'nadi_keluar' => 'Nadi',
            'pernapasan_keluar' => 'Pernapasan',
            'saturasi_o2_keluar' => 'Saturasi O2',
            'suhu_keluar' => 'Suhu',
            'kasus_kecelakaan' => 'Kecelakaan',
            'objective' => 'Objektif',
        ];
    }
}
