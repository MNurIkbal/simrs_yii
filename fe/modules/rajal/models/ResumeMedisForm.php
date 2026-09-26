<?php

namespace app\modules\rajal\models;

use Yii;

class ResumeMedisForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $diag_awal;
    public $diag_awal_text;
    public $diag_utama;
    public $diag_utama_text;
    public $diag_penyerta;
    public $diag_penyerta_json;
    public $riwayat_penyakit_dahulu;
    public $indikasi_pasien_dirawat;
    public $reaksi_alergi_obat;
    public $kondisi_pulang;
    public $rencana_tindaklanjut;
    public $instruksi;

    //anamnesa
    public $keluhan_utama;
    public $metod_asmennyeri;
    public $riwayat_alergi;
    public $alergi_obat;
    public $alergi_makanan;
    public $alergi_lainnya;
    public $skala;
    public $obat_diberikan;
    public $anamnesa;

    //pemeriksaan fisik
    public $berat_badan;
    public $tinggi_badan;
    public $td;
    public $nadi;
    public $rr;
    public $suhu;
    public $pemeriksaan_fisik;

    //order list
    public $order_laboratorium;
    public $order_radiologi;
    public $konsul;
    public $obat;
    public $obat_dibawa_pulang;
    public $obat_dibawa_pulang_text;
    public $tindakan;
    public $prosedur;
    public $prosedur_list;
    public $konsultasi;
    public $obat_rs;
    public $lain_lainnya;
    public $catatan_diet;

    public $additional_data;
    public $tgl_masuk;
    public $tgl_keluar;
    public $keadaan_umum;
    public $kesadaran;
    public $frekuensi_nafas;
    public $cara_keluar;
    public $instruksi_kontrol;
    public $instruksi_tanggal;
    public $is_igd;
    public $kontak_darurat;
    public $edukasi_rencana;
    public $instruksi_tindakanbmhp;
    public $is_alergi;
    public $nama_alergi;
    public $dokter_pengirim;
    
    public $resumemedisri_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    'diag_awal',
                    'diag_awal_text',
                    'diag_utama',
                    'diag_utama_text',
                    'diag_penyerta',
                    'diag_penyerta_json',
                    'riwayat_penyakit_dahulu',
                    'indikasi_pasien_dirawat',
                    'reaksi_alergi_obat',
                    'kondisi_pulang',
                    'tindakan',
                    'keluhan_utama',
                    'metod_asmennyeri',
                    'riwayat_alergi',
                    'alergi_obat',
                    'alergi_makanan',
                    'alergi_lainnya',
                    'skala',
                    'obat_diberikan',
                    'berat_badan',
                    'tinggi_badan',
                    'td',
                    'nadi',
                    'rr',
                    'suhu',
                    'rencana_tindaklanjut',
                    'order_laboratorium',
                    'order_radiologi',
                    'konsul',
                    'obat',
                    'obat_dibawa_pulang',
                    'obat_dibawa_pulang_text',
                    'instruksi',
                    'anamnesa',
                    'pemeriksaan_fisik',
                    'prosedur',
                    'prosedur_list',
                    'konsultasi',
                    'obat_rs',
                    'lain_lainnya',
                    'catatan_diet',
                    'additional_data',
                    'tgl_masuk',
                    'tgl_keluar',
                    'keadaan_umum',
                    'kesadaran',
                    'frekuensi_nafas',
                    'cara_keluar',
                    'instruksi_kontrol',
                    'instruksi_tanggal',
                    'kontak_darurat',
                    'is_igd',
                    'edukasi_rencana',
                    'instruksi_tindakanbmhp',
                    'is_alergi',
                    'nama_alergi',
                    'dokter_pengirim',
                    'resumemedisri_id',
                ],
                'safe'
            ],
            [
                [
                    'pendaftaran_id',
                    'diag_awal',
                    'diag_utama',
                ], 
                'required'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => \Yii::t('fe', 'PendaftaranID'),
            'diag_masuk' => \Yii::t('fe', 'Diagnosa Masuk'),
            'diag_keluar' => \Yii::t('fe', 'Diagnosa Masuk'),
            'diag_utama' => \Yii::t('fe', 'Diagnosa Utama'),
            'diag_utama_text' => \Yii::t('fe', 'Diagnosa Masuk'),
            'diag_penyerta' => \Yii::t('fe', 'Diagnosa Penyerta'),
            'a_f_bermakna' => \Yii::t('fe', 'Anamnesis,Pemeriksaan Fisik & Penunjang yang bermakna'),
            'prosedur_diag' => \Yii::t('fe', 'Prosedur Diagnostik & Terapetik'),
            'kondisipulang_id' => \Yii::t('fe', 'Kondisi Pulang'),
            'tgl_kontrol' => \Yii::t('fe', 'Tanggal'),
            'rencana_tindaklanjut' => \Yii::t('fe', 'Rencana tindak lanjut'),
            'carakeluar_id' => \Yii::t('fe', 'Cara Keluar'),
            'diag_awal' => \Yii::t('fe', 'Diagnosa Masuk'),
            'diag_awal_text' => \Yii::t('fe', 'Diagnosa Masuk'),
            'tgl_masuk' => \Yii::t('fe', 'Tanggal Masuk'),
            'tgl_keluar' => \Yii::t('fe', 'Tanggal Keluar'),
            'riwayat_penyakit_dahulu' => \Yii::t('fe', 'Riwayat Penyakit'),
            'pemeriksaan_fisik' => \Yii::t('fe', 'Pemeriksaan Fisik & Keadaan Umum'),
            'kesadaran' => \Yii::t('fe', 'Kesadaran'),
            'keadaan_umum' => \Yii::t('fe', 'Keadaan Umum'),
            'td' => \Yii::t('fe', 'Tekanan Darah'),
            'suhu' => \Yii::t('fe', 'Suhu'),
            'nadi' => \Yii::t('fe', 'Nadi'),
            'frekuensi_nafas' => \Yii::t('fe', 'Frekuensi Nafas'),
            'obat_rs' => 'Obat-obatan selama di rumah sakit',
            'dokter_pengirim' => \Yii::t('fe', 'Dokter Pengirim dari Luar'),

        ];
    }
}
