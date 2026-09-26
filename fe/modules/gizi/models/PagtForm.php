<?php

namespace app\modules\gizi\models;

use Yii;

class PagtForm extends \yii\base\Model
{
    public $pagt_id;
    public $pendaftaran_id;
    public $tgl_kajian;
    public $diagnosa_medis;
    public $diet;
    public $pandangan_alergi;
    public $bb;
    public $tb;
    public $lila;
    public $imt_dewasa;
    public $imt_anak;
    public $bb_anak;
    public $ulna;
    //belum ada di db status_gizi
    public $status_gizi;
    public $trigliserida;
    public $hdl;
    public $ldl;
    public $kolesterol;
    public $ureum;
    public $kreatinin;
    public $kalium;
    public $natrium;
    public $kalsium;
    public $phospor;
    public $sgot;
    public $sgpt;
    public $bilirubin;
    public $gd_sewaktu;
    public $gd_puasa;
    public $hba1c;
    public $dua_jam_pp;
    public $hb;
    public $albumin;
    public $ht;
    public $pemeriksaan_fisik;
    public $pemeriksaan_fisik_lainnnya;
    public $tekanan_darah;
    public $gangguan_pencernaan;
    public $makan_pagi_pokok;
    public $makan_pagi_hewani;
    public $makan_pagi_nabati;
    public $makan_pagi_sayur;
    public $makan_pagi_buah;
    public $makan_pagi_energi;
    public $makan_pagi_protein;
    public $makan_pagi_lemak;
    public $makan_pagi_kh;
    public $selingan_pagi_pokok;
    public $selingan_pagi_hewani;
    public $selingan_pagi_nabati;
    public $selingan_pagi_sayur;
    public $selingan_pagi_buah;
    public $selingan_pagi_energi;
    public $selingan_pagi_protein;
    public $selingan_pagi_lemak;
    public $selingan_pagi_kh;
    public $makan_siang_pokok;
    public $makan_siang_hewani;
    public $makan_siang_nabati;
    public $makan_siang_sayur;
    public $makan_siang_buah;
    public $makan_siang_energi;
    public $makan_siang_protein;
    public $makan_siang_lemak;
    public $makan_siang_kh;
    public $selingan_sore_pokok;
    public $selingan_sore_hewani;
    public $selingan_sore_nabati;
    public $selingan_sore_sayur;
    public $selingan_sore_buah;
    public $selingan_sore_energi;
    public $selingan_sore_protein;
    public $selingan_sore_lemak;
    public $selingan_sore_kh;
    public $makan_malam_pokok;
    public $makan_malam_hewani;
    public $makan_malam_nabati;
    public $makan_malam_sayur;
    public $makan_malam_buah;
    public $makan_malam_energi;
    public $makan_malam_protein;
    public $makan_malam_lemak;
    public $makan_malam_kh;
    public $selingan_malam_pokok;
    public $selingan_malam_hewani;
    public $selingan_malam_nabati;
    public $selingan_malam_sayur;
    public $selingan_malam_buah;
    public $selingan_malam_energi;
    public $selingan_malam_protein;
    public $selingan_malam_lemak;
    public $selingan_malam_kh;
    public $total_pokok;
    public $total_hewani;
    public $total_nabati;
    public $total_sayur;
    public $total_buah;
    public $total_energi;
    public $total_protein;
    public $total_lemak;
    public $total_kh;
    public $alergi;
    public $diet_dijalankan;
    public $olah_raga_hari;
    public $olah_raga_menit;
    public $kebiasaan_merokok;
    public $aktifitas_fisik;
    public $diagnosa_gizi;
    //belum ada di db cara_intervensi
    public $cara_intervensi;
    public $diet_diberikan;
    public $energi;
    public $lemak;
    public $protein;
    public $kh;
    public $tujuan_diet;
    public $bentuk_makanan;
    public $bentuk_makanan_saji;
    public $bentuk_makanan_hari;
    public $cara_pemberian;
    public $cara_pemberian_vitamin;
    public $bagi_makan_pagi_nasi;
    public $bagi_makan_pagi_hewani;
    public $bagi_makan_pagi_nabati;
    public $bagi_makan_pagi_sayur;
    public $bagi_makan_pagi_buah;
    public $bagi_selingan_pagi_nasi;
    public $bagi_selingan_pagi_hewani;
    public $bagi_selingan_pagi_nabati;
    public $bagi_selingan_pagi_sayur;
    public $bagi_selingan_pagi_buah;
    public $bagi_makan_siang_nasi;
    public $bagi_makan_siang_hewani;
    public $bagi_makan_siang_nabati;
    public $bagi_makan_siang_sayur;
    public $bagi_makan_siang_buah;
    public $bagi_selingan_sore_nasi;
    public $bagi_selingan_sore_hewani;
    public $bagi_selingan_sore_nabati;
    public $bagi_selingan_sore_sayur;
    public $bagi_selingan_sore_buah;
    public $bagi_makan_malam_nasi;
    public $bagi_makan_malam_hewani;
    public $bagi_makan_malam_nabati;
    public $bagi_makan_malam_sayur;
    public $bagi_makan_malam_buah;
    public $bagi_selingan_malam_nasi;
    public $bagi_selingan_malam_hewani;
    public $bagi_selingan_malam_nabati;
    public $bagi_selingan_malam_sayur;
    public $bagi_selingan_malam_buah;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;


    public function rules()
    {
        return [
            [
                [
                    'pagt_id',
                    'pendaftaran_id',
                    'tgl_kajian',
                    'diagnosa_medis',
                    'diet',
                    'pandangan_alergi',
                    'bb',
                    'tb',
                    'lila',
                    'imt_dewasa',
                    'imt_anak',
                    'bb_anak',
                    'ulna',
                    //belum ada di db status_gizi
                    'status_gizi',
                    'trigliserida',
                    'hdl',
                    'ldl',
                    'kolesterol',
                    'ureum',
                    'kreatinin',
                    'kalium',
                    'natrium',
                    'kalsium',
                    'phospor',
                    'sgot',
                    'sgpt',
                    'bilirubin',
                    'gd_sewaktu',
                    'gd_puasa',
                    'hba1c',
                    'dua_jam_pp',
                    'hb',
                    'albumin',
                    'ht',
                    'pemeriksaan_fisik',
                    'pemeriksaan_fisik_lainnnya',
                    'tekanan_darah',
                    'gangguan_pencernaan',
                    'makan_pagi_pokok',
                    'makan_pagi_hewani',
                    'makan_pagi_nabati',
                    'makan_pagi_sayur',
                    'makan_pagi_buah',
                    'makan_pagi_energi',
                    'makan_pagi_protein',
                    'makan_pagi_lemak',
                    'makan_pagi_kh',
                    'selingan_pagi_pokok',
                    'selingan_pagi_hewani',
                    'selingan_pagi_nabati',
                    'selingan_pagi_sayur',
                    'selingan_pagi_buah',
                    'selingan_pagi_energi',
                    'selingan_pagi_protein',
                    'selingan_pagi_lemak',
                    'selingan_pagi_kh',
                    'makan_siang_pokok',
                    'makan_siang_hewani',
                    'makan_siang_nabati',
                    'makan_siang_sayur',
                    'makan_siang_buah',
                    'makan_siang_energi',
                    'makan_siang_protein',
                    'makan_siang_lemak',
                    'makan_siang_kh',
                    'selingan_sore_pokok',
                    'selingan_sore_hewani',
                    'selingan_sore_nabati',
                    'selingan_sore_sayur',
                    'selingan_sore_buah',
                    'selingan_sore_energi',
                    'selingan_sore_protein',
                    'selingan_sore_lemak',
                    'selingan_sore_kh',
                    'makan_malam_pokok',
                    'makan_malam_hewani',
                    'makan_malam_nabati',
                    'makan_malam_sayur',
                    'makan_malam_buah',
                    'makan_malam_energi',
                    'makan_malam_protein',
                    'makan_malam_lemak',
                    'makan_malam_kh',
                    'selingan_malam_pokok',
                    'selingan_malam_hewani',
                    'selingan_malam_nabati',
                    'selingan_malam_sayur',
                    'selingan_malam_buah',
                    'selingan_malam_energi',
                    'selingan_malam_protein',
                    'selingan_malam_lemak',
                    'selingan_malam_kh',
                    'total_pokok',
                    'total_hewani',
                    'total_nabati',
                    'total_sayur',
                    'total_buah',
                    'total_energi',
                    'total_protein',
                    'total_lemak',
                    'total_kh',
                    'alergi',
                    'diet_dijalankan',
                    'olah_raga_hari',
                    'olah_raga_menit',
                    'kebiasaan_merokok',
                    'aktifitas_fisik',
                    //belum ada di db cara_intervensi
                    'cara_intervensi',
                    'diagnosa_gizi',
                    'diet_diberikan',
                    'energi',
                    'lemak',
                    'protein',
                    'kh',
                    'tujuan_diet',
                    'bentuk_makanan',
                    'bentuk_makanan_saji',
                    'bentuk_makanan_hari',
                    'cara_pemberian',
                    'cara_pemberian_vitamin',
                    'bagi_makan_pagi_nasi',
                    'bagi_makan_pagi_hewani',
                    'bagi_makan_pagi_nabati',
                    'bagi_makan_pagi_sayur',
                    'bagi_makan_pagi_buah',
                    'bagi_selingan_pagi_nasi',
                    'bagi_selingan_pagi_hewani',
                    'bagi_selingan_pagi_nabati',
                    'bagi_selingan_pagi_sayur',
                    'bagi_selingan_pagi_buah',
                    'bagi_makan_siang_nasi',
                    'bagi_makan_siang_hewani',
                    'bagi_makan_siang_nabati',
                    'bagi_makan_siang_sayur',
                    'bagi_makan_siang_buah',
                    'bagi_selingan_sore_nasi',
                    'bagi_selingan_sore_hewani',
                    'bagi_selingan_sore_nabati',
                    'bagi_selingan_sore_sayur',
                    'bagi_selingan_sore_buah',
                    'bagi_makan_malam_nasi',
                    'bagi_makan_malam_hewani',
                    'bagi_makan_malam_nabati',
                    'bagi_makan_malam_sayur',
                    'bagi_makan_malam_buah',
                    'bagi_selingan_malam_nasi',
                    'bagi_selingan_malam_hewani',
                    'bagi_selingan_malam_nabati',
                    'bagi_selingan_malam_sayur',
                    'bagi_selingan_malam_buah',
                    'additional_data',
                    'created_date',
                    'created_by',
                    'modified_count',
                    'last_modified_date',
                    'last_modified_by',
                    'is_deleted',
                    'is_active',
                    'deleted_date',
                    'deleted_by',

                ],
                'safe'
            ],
            [
              ['bb', 'tb', 'imt_dewasa'],
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
            'pagt_id'                    => 'Pagt Id',
            'pendaftaran_id'             => 'Pendaftaran Id',
            'tgl_kajian'                 => 'Tanggal/Jam Kajian',
            'diagnosa_medis'             => 'Diagnosa Medis',
            'diet'                       => 'Diet',
            'pandangan_alergi'           => 'Pantangan Alergi',
            'bb'                         => 'BB',
            'tb'                         => 'TB',
            'lila'                       => 'LILA',
            'imt_dewasa'                 => 'IMT (Dewasa)',
            'imt_anak'                   => 'IMT/U (Anak)',
            'bb_anak'                    => 'Bb/U Anak',
            'ulna'                       => 'ULNA',
            //belum ada di db status_gizi
            'status_gizi'                => 'Status Gizi',
            'trigliserida'               => 'Trigliserida',
            'hdl'                        => 'HDL',
            'ldl'                        => 'LDL',
            'kolesterol'                 => 'Kolesterol',
            'ureum'                      => 'Ureum',
            'kreatinin'                  => 'Kreatinin',
            'kalium'                     => 'Kalium',
            'natrium'                    => 'Natrium',
            'kalsium'                    => 'Kalsium',
            'phospor'                    => 'Phospor',
            'sgot'                       => 'SGOT',
            'sgpt'                       => 'SGPT',
            'bilirubin'                  => 'Bilurbin',
            'gd_sewaktu'                 => 'GD Sewaktu',
            'gd_puasa'                   => 'GD Puasa',
            'hba1c'                      => 'HBA1C',
            'dua_jam_pp'                 => '2 Jam PP',
            'hb'                         => 'HB',
            'albumin'                    => 'Albumin',
            'ht'                         => 'HT',
            'pemeriksaan_fisik'          => 'Pemeriksaan Fisik',
            'pemeriksaan_fisik_lainnnya' => 'Pemeriksaan Fisik Lainnnya',
            'tekanan_darah'              => 'Tekanan Darah',
            'gangguan_pencernaan'        => 'Gangguan Pencernaan',
            'makan_pagi_pokok'           => 'Makan Pagi Pokok',
            'makan_pagi_hewani'          => 'Makan Pagi Hewani',
            'makan_pagi_nabati'          => 'Makan Pagi Nabati',
            'makan_pagi_sayur'           => 'Makan Pagi Sayur',
            'makan_pagi_buah'            => 'Makan Pagi Buah',
            'makan_pagi_energi'          => 'Makan Pagi Energi',
            'makan_pagi_protein'         => 'Makan Pagi Protein',
            'makan_pagi_lemak'           => 'Makan Pagi Lemak',
            'makan_pagi_kh'              => 'Makan Pagi KH',
            'selingan_pagi_pokok'        => 'Selingan Pagi Pokok',
            'selingan_pagi_hewani'       => 'Selingan Pagi Hewani',
            'selingan_pagi_nabati'       => 'Selingan Pagi Nabati',
            'selingan_pagi_sayur'        => 'Selingan Pagi Sayur',
            'selingan_pagi_buah'         => 'Selingan Pagi Buah',
            'selingan_pagi_energi'       => 'Selingan Pagi Energi',
            'selingan_pagi_protein'      => 'Selingan Pagi Protein',
            'selingan_pagi_lemak'        => 'Selingan Pagi Lemak',
            'selingan_pagi_kh'           => 'Selingan Pagi KH',
            'makan_siang_pokok'          => 'Makan Siang Pokok',
            'makan_siang_hewani'         => 'Makan Siang Hewani',
            'makan_siang_nabati'         => 'Makan Siang Nabati',
            'makan_siang_sayur'          => 'Makan Siang Sayur',
            'makan_siang_buah'           => 'Makan Siang Buah',
            'makan_siang_energi'         => 'Makan Siang Energi',
            'makan_siang_protein'        => 'Makan Siang Protein',
            'makan_siang_lemak'          => 'Makan Siang Lemak',
            'makan_siang_kh'             => 'Makan Siang KH',
            'selingan_sore_pokok'        => 'Selingan Sore Pokok',
            'selingan_sore_hewani'       => 'Selingan Sore Hewani',
            'selingan_sore_nabati'       => 'Selingan Sore Nabati',
            'selingan_sore_sayur'        => 'Selingan Sore Sayur',
            'selingan_sore_buah'         => 'Selingan Sore Buah',
            'selingan_sore_energi'       => 'Selingan Sore Energi',
            'selingan_sore_protein'      => 'Selingan Sore Protein',
            'selingan_sore_lemak'        => 'Selingan Sore Lemak',
            'selingan_sore_kh'           => 'Selingan Sore KH',
            'makan_malam_pokok'          => 'Makan Malam Pokok',
            'makan_malam_hewani'         => 'Makan Malam Hewani',
            'makan_malam_nabati'         => 'Makan Malam Nabati',
            'makan_malam_sayur'          => 'Makan Malam Sayur',
            'makan_malam_buah'           => 'Makan Malam Buah',
            'makan_malam_energi'         => 'Makan Malam Energi',
            'makan_malam_protein'        => 'Makan Malam Protein',
            'makan_malam_lemak'          => 'Makan Malam Lemak',
            'makan_malam_kh'             => 'Makan Malam KH',
            'selingan_malam_pokok'       => 'Selingan Malam Pokok',
            'selingan_malam_hewani'      => 'Selingan Malam Hewani',
            'selingan_malam_nabati'      => 'Selingan Malam Nabati',
            'selingan_malam_sayur'       => 'Selingan Malam Sayur',
            'selingan_malam_buah'        => 'Selingan Malam Buah',
            'selingan_malam_energi'      => 'Selingan Malam Energi',
            'selingan_malam_protein'     => 'Selingan Malam Protein',
            'selingan_malam_lemak'       => 'Selingan Malam Lemak',
            'selingan_malam_kh'          => 'Selingan Malam KH',
            'total_pokok'                => 'Total Pokok',
            'total_hewani'               => 'Total Hewani',
            'total_nabati'               => 'Total Nabati',
            'total_sayur'                => 'Total Sayur',
            'total_buah'                 => 'Total Buah',
            'total_energi'               => 'Total Energi',
            'total_protein'              => 'Total Protein',
            'total_lemak'                => 'Total Lemak',
            'total_kh'                   => 'Total KH',
            'alergi'                     => 'Alergi/Pantangan',
            'diet_dijalankan'            => 'Diet yang Dijalankan',
            'olah_raga_hari'             => 'Frekuensi Olah Raga',
            'olah_raga_menit'            => 'Olah Raga Menit',
            'kebiasaan_merokok'          => 'Kebiasaan Merokok',
            'aktifitas_fisik'            => 'Aktifitas Fisik',
            'diagnosa_gizi'              => 'Diagnosa Gizi',
            //belum ada di db cara_intervensi
            'cara_intervensi'            => 'Cara Intervensi',
            'diet_diberikan'             => 'Rencana Diet Yang Diberikan',
            'energi'                     => 'Energi',
            'lemak'                      => 'Lemak',
            'protein'                    => 'Protein',
            'kh'                         => 'KH',
            'tujuan_diet'                => 'Tujuan Diet',
            'bentuk_makanan'             => 'Bentuk Makanan',
            'bentuk_makanan_saji'        => 'Bentuk Makanan Saji',
            'bentuk_makanan_hari'        => 'Bentuk Makanan Hari',
            'cara_pemberian'             => 'Cara Pemberian',
            'cara_pemberian_vitamin'     => 'Cara Pemberian Vitamin',
            'bagi_makan_pagi_nasi'       => 'Bagi Makan Pagi Nasi',
            'bagi_makan_pagi_hewani'     => 'Bagi Makan Pagi Hewani',
            'bagi_makan_pagi_nabati'     => 'Bagi Makan Pagi Nabati',
            'bagi_makan_pagi_sayur'      => 'Bagi Makan Pagi Sayur',
            'bagi_makan_pagi_buah'       => 'Bagi Makan Pagi Buah',
            'bagi_selingan_pagi_nasi'    => 'Bagi Selingan Pagi Nasi',
            'bagi_selingan_pagi_hewani'  => 'Bagi Selingan Pagi Hewani',
            'bagi_selingan_pagi_nabati'  => 'Bagi Selingan Pagi Nabati',
            'bagi_selingan_pagi_sayur'   => 'Bagi Selingan Pagi Sayur',
            'bagi_selingan_pagi_buah'    => 'Bagi Selingan Pagi Buah',
            'bagi_makan_siang_nasi'      => 'Bagi Makan Siang Nasi',
            'bagi_makan_siang_hewani'    => 'Bagi Makan Siang Hewani',
            'bagi_makan_siang_nabati'    => 'Bagi Makan Siang Nabati',
            'bagi_makan_siang_sayur'     => 'Bagi Makan Siang Sayur',
            'bagi_makan_siang_buah'      => 'Bagi Makan Siang Buah',
            'bagi_selingan_sore_nasi'    => 'Bagi Selingan Sore Nasi',
            'bagi_selingan_sore_hewani'  => 'Bagi Selingan Sore Hewani',
            'bagi_selingan_sore_nabati'  => 'Bagi Selingan Sore Nabati',
            'bagi_selingan_sore_sayur'   => 'Bagi Selingan Sore Sayur',
            'bagi_selingan_sore_buah'    => 'Bagi Selingan Sore Buah',
            'bagi_makan_malam_nasi'      => 'Bagi Makan Malam Nasi',
            'bagi_makan_malam_hewani'    => 'Bagi Makan Malam Hewani',
            'bagi_makan_malam_nabati'    => 'Bagi Makan Malam Nabati',
            'bagi_makan_malam_sayur'     => 'Bagi Makan Malam Sayur',
            'bagi_makan_malam_buah'      => 'Bagi Makan Malam Buah',
            'bagi_selingan_malam_nasi'   => 'Bagi Selingan Malam Nasi',
            'bagi_selingan_malam_hewani' => 'Bagi Selingan Malam Hewani',
            'bagi_selingan_malam_nabati' => 'Bagi Selingan Malam Nabati',
            'bagi_selingan_malam_sayur'  => 'Bagi Selingan Malam Sayur',
            'bagi_selingan_malam_buah'   => 'Bagi Selingan Malam Buah',
            'additional_data'            => 'Additional Data',
            'created_date'               => 'Created Date',
            'created_by'                 => 'Created By',
            'modified_count'             => 'Modified Count',
            'last_modified_date'         => 'Last Modified Date',
            'last_modified_by'           => 'Last Modified By',
            'is_deleted'                 => 'Is Deleted',
            'is_active'                  => 'Is Active',
            'deleted_date'               => 'Deleted Date',
            'deleted_by'                 => 'Deleted By',
        ];
    }
}
