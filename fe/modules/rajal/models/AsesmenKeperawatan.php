<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 18:00:01
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-05-22 13:40:45
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class AsesmenKeperawatan extends \yii\base\Model
{
    public $no_pendaftaran;
    public $sumber_data;
    public $pegawaidokter_id;
    public $dokter_nama;
    public $pegawaiperawat_id;
    public $perawat_nama;
    public $rujukan;
    public $tujuan_rujukan;
    public $rujukan_rs;
    public $diagnosa_rujukan;
    public $tgl_anamnesis;
    public $keluhan_utama;
    public $berat_badan;
    public $tinggi_badan;
    public $nadi;
    public $rr;
    public $td;
    public $suhu;
    public $riwayat_penyakit;
    public $riwayat_penyakit_nama;
    public $dirawat;
    public $dirawat_diagnosa;
    public $dirawat_waktu;
    public $dirawat_tempat;
    public $dioperasi;
    public $dioperasi_diagnosa;
    public $dioperasi_waktu;
    public $obat_dikonsumsi;
    public $obat_dikonsumsi_nama;
    public $riwayat_penyakit_keluarga;
    public $riwayat_penyakit_keluarga_list;
    public $ketergantungan;
    public $ketergantungan_jenis;
    public $riwayat_pekerjaan;
    public $riwayat_pekerjaan_nama;
    public $alergi;
    public $alergi_obat;
    public $alergi_makanan;
    public $alergi_lainnya;
    public $reaksi_alergi;
    public $status_psikologi;
    public $status_sosial;
    public $nama_kerabat_terdekat;
    public $hubungan_kerabat_terdekat;
    public $kontak_kerabat_terdekat;
    public $status_ekonomi;
    public $nilai_kebudayaan;
    public $suku_id;
    public $suku_nama;
    public $hambatan;
    public $jenis_hambatan;
    public $butuh_penerjemah;
    public $butuh_penerjemah_nama;
    public $bahasa_isyarat;
    public $kesediaan_menerima_informasi;
    public $kemampuan_membaca;
    public $bahasa;
    public $kebutuhan_edukasi;
    public $kebutuhan_edukasi_lainnya;
    public $kebutuhan_edukasi_keperawatan;
    public $resiko_cedera_pertama;
    public $resiko_cedera_kedua;
    public $hasil_resiko;
    public $aktivitas;
    public $bantuan_aktivitas;
    public $alat_bantu_jalan;
    public $is_nyeri;
    public $skala_nyeri;
    public $nyeri_kronis_pertama;
    public $lokasi_nyeri_kronis_pertama;
    public $frekuensi_nyeri_kronis_pertama;
    public $durasi_nyeri_kronis_pertama;
    public $nyeri_kronis_kedua;
    public $lokasi_nyeri_kronis_kedua;
    public $frekuensi_nyeri_kronis_kedua;
    public $durasi_nyeri_kronis_kedua;
    public $skor_nyeri;
    public $nyeri_menjalar;
    public $kualitas_nyeri;
    public $faktor_pereda_nyeri;
    public $nutrisi_1a;
    public $nutrisi_1b;
    public $nutrisi_2;
    public $nilai_nutrisi;
    public $diagnosa_khusus;
    public $jenis_diagnosa_khusus;
    public $diagnosa_keperawatan;
    public $strongkids_kurus;
    public $strongkids_turunbb;
    public $strongkids_kondisikhusus;
    public $strongkids_keadaan_beresiko;
    public $is_verifikasigizi;
    public $pegawaiverifikasigizi_id;
    public $pegawaiverifikasigizi_nama;
    public $tgl_verifikasigizi;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'pegawaiperawat_id',
                    'keluhan_utama',
                    'berat_badan',
                    'tinggi_badan',
                    'nadi',
                    'rr',
                    'td',
                    'suhu',
                    'tgl_anamnesis'
                ], 'required', 'message' => '{attribute} ' . Yii::t('fe', 'Tidak boleh kosong')
            ],
            [
                [
                    'sumber_data',
                    'pegawaidokter_id',
                    'dokter_nama',
                    'pegawaiperawat_id',
                    'perawat_nama',
                    'rujukan',
                    'rujukan_rs',
                    'tujuan_rujukan',
                    'diagnosa_rujukan',
                    'riwayat_penyakit',
                    'riwayat_penyakit_nama',
                    'dirawat',
                    'dirawat_diagnosa',
                    'dirawat_waktu',
                    'dirawat_tempat',
                    'dioperasi',
                    'dioperasi_diagnosa',
                    'dioperasi_waktu',
                    'obat_dikonsumsi',
                    'obat_dikonsumsi_nama',
                    'riwayat_penyakit_keluarga',
                    'riwayat_penyakit_keluarga_list',
                    'ketergantungan',
                    'ketergantungan_jenis',
                    'riwayat_pekerjaan',
                    'riwayat_pekerjaan_nama',
                    'alergi',
                    'alergi_obat',
                    'alergi_makanan',
                    'alergi_lainnya',
                    'reaksi_alergi',
                    'status_psikologi',
                    'status_sosial',
                    'nama_kerabat_terdekat',
                    'hubungan_kerabat_terdekat',
                    'kontak_kerabat_terdekat',
                    'status_ekonomi',
                    'nilai_kebudayaan',
                    'suku_id',
                    'suku_nama',
                    'hambatan',
                    'jenis_hambatan',
                    'butuh_penerjemah',
                    'butuh_penerjemah_nama',
                    'bahasa_isyarat',
                    'kesediaan_menerima_informasi',
                    'kemampuan_membaca',
                    'bahasa',
                    'kebutuhan_edukasi',
                    'kebutuhan_edukasi_lainnya',
                    'kebutuhan_edukasi_keperawatan',
                    'resiko_cedera_pertama',
                    'resiko_cedera_kedua',
                    'hasil_resiko',
                    'aktivitas',
                    'bantuan_aktivitas',
                    'alat_bantu_jalan',
                    'is_nyeri',
                    'skala_nyeri',
                    'nyeri_kronis_pertama',
                    'lokasi_nyeri_kronis_pertama',
                    'frekuensi_nyeri_kronis_pertama',
                    'durasi_nyeri_kronis_pertama',
                    'nyeri_kronis_kedua',
                    'lokasi_nyeri_kronis_kedua',
                    'frekuensi_nyeri_kronis_kedua',
                    'durasi_nyeri_kronis_kedua',
                    'skor_nyeri',
                    'nyeri_menjalar',
                    'kualitas_nyeri',
                    'faktor_pereda_nyeri',
                    'nutrisi_1a',
                    'nutrisi_1b',
                    'nutrisi_2',
                    'nilai_nutrisi',
                    'diagnosa_khusus',
                    'jenis_diagnosa_khusus',
                    'diagnosa_keperawatan',
                    'strongkids_kurus',
                    'strongkids_turunbb',
                    'strongkids_kondisikhusus',
                    'strongkids_keadaan_beresiko',
                    'is_verifikasigizi',
                    'pegawaiverifikasigizi_id',
                    'pegawaiverifikasigizi_nama',
                    'tgl_verifikasigizi'
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_pendaftaran' => Yii::t('fe', 'No Pendaftaran'),
            'tgl_anamnesis' => Yii::t('fe', 'Tanggal Anamesa'),
            'sumber_data' => Yii::t('fe', 'Sumber Data'),
            'pegawaidokter_id' => Yii::t('fe', 'Dokter Pemeriksa'),
            'dokter_nama' => Yii::t('fe', 'Dokter Pemeriksa'),
            'pegawaiperawat_id' => Yii::t('fe', 'Perawat Pemeriksa'),
            'perawat_nama' => Yii::t('fe', 'Perawat Pemeriksa'),
            'rujukan' => Yii::t('fe', 'Rujukan'),
            'rujukan_rs' => Yii::t('fe', 'Rujukan RS'),
            'diagnosa_rujukan' => Yii::t('fe', 'Diagnosa Rujukan'),
            'keluhan_utama' => Yii::t('fe', 'Keluhan Utama'),
            'berat_badan' => Yii::t('fe', 'Berat Badan'),
            'tinggi_badan' => Yii::t('fe', 'Tinggi Badan'),
            'nadi' => Yii::t('fe', 'Nadi'),
            'rr' => Yii::t('fe', 'RR'),
            'td' => Yii::t('fe', 'TD'),
            'suhu' => Yii::t('fe', 'Suhu'),
            'riwayat_penyakit' => Yii::t('fe', 'Riwayat Penyakit'),
            'riwayat_penyakit_nama' => Yii::t('fe', ''),
            'dirawat' => Yii::t('fe', ''),
            'dirawat_diagnosa' => Yii::t('fe', 'Diagnosa Dirawat'),
            'dirawat_waktu' => Yii::t('fe', 'Waktu Dirawat'),
            'dirawat_tempat' => Yii::t('fe', 'Tempat Dirawat'),
            'dioperasi' => Yii::t('fe', ''),
            'dioperasi_jenis' => Yii::t('fe', ''),
            'dioperasi_waktu' => Yii::t('fe', ''),
            'obat_dikonsumsi' => Yii::t('fe', ''),
            'obat_dikonsumsi_nama' => Yii::t('fe', ''),
            'riwayat_penyakit_keluarga' => Yii::t('fe', ''),
            'riwayat_penyakit_keluarga_list' => Yii::t('fe', ''),
            'ketergantungan' => Yii::t('fe', ''),
            'ketergantungan_jenis' => Yii::t('fe', ''),
            'riwayat_pekerjaan' => Yii::t('fe', ''),
            'riwayat_pekerjaan_nama' => Yii::t('fe', ''),
            'alergi' => Yii::t('fe', ''),
            'alergi_obat' => Yii::t('fe', ''),
            'alergi_makanan' => Yii::t('fe', ''),
            'alergi_lainnya' => Yii::t('fe', ''),
            'reaksi_alergi' => Yii::t('fe', ''),
            'status_psikologi' => Yii::t('fe', ''),
            'status_sosial' => Yii::t('fe', ''),
            'nama_kerabat_terdekat' => Yii::t('fe', 'Nama'),
            'hubungan_kerabat_terdekat' => Yii::t('fe', 'Hubungan'),
            'kontak_kerabat_terdekat' => Yii::t('fe', 'No Telepon'),
            'status_ekonomi' => Yii::t('fe', ''),
            'nilai_kebudayaan' => Yii::t('fe', ''),
            'suku_id' => Yii::t('fe', 'Suku'),
            'suku_nama' => Yii::t('fe', 'Suku'),
            'hambatan' => Yii::t('fe', ''),
            'jenis_hambatan' => Yii::t('fe', ''),
            'butuh_penerjemah' => Yii::t('fe', ''),
            'butuh_penerjemah_nama' => Yii::t('fe', ''),
            'bahasa_isyarat' => Yii::t('fe', ''),
            'kesediaan_menerima_informasi' => Yii::t('fe', ''),
            'kemampuan_membaca' => Yii::t('fe', ''),
            'bahasa' => Yii::t('fe', ''),
            'kebutuhan_edukasi' => Yii::t('fe', ''),
            'kebutuhan_edukasi_lainnya' => Yii::t('fe', ''),
            'kebutuhan_edukasi_keperawatan' => Yii::t('fe', ''),
            'resiko_cedera_pertama' => Yii::t('fe', ''),
            'resiko_cedera_kedua' => Yii::t('fe', ''),
            'hasil_resiko' => Yii::t('fe', ''),
            'aktivitas' => Yii::t('fe', ''),
            'bantuan_aktivitas' => Yii::t('fe', ''),
            'alat_bantu_jalan' => Yii::t('fe', 'Alat Bantu'),
            'is_nyeri' => Yii::t('fe', ''),
            'skala_nyeri' => Yii::t('fe', ''),
            'nyeri_kronis_pertama' => Yii::t('fe', ''),
            'lokasi_nyeri_kronis_pertama' => Yii::t('fe', 'Lokasi'),
            'frekuensi_nyeri_kronis_pertama' => Yii::t('fe', ''),
            'durasi_nyeri_kronis_pertama' => Yii::t('fe', 'Durasi/Lama nyeri'),
            'nyeri_kronis_kedua' => Yii::t('fe', ''),
            'lokasi_nyeri_kronis_kedua' => Yii::t('fe', 'Lokasi'),
            'frekuensi_nyeri_kronis_kedua' => Yii::t('fe', ''),
            'durasi_nyeri_kronis_kedua' => Yii::t('fe', 'Durasi/Lama nyeri'),
            'skor_nyeri' => Yii::t('fe', 'Skor Nyeri'),
            'nyeri_menjalar' => Yii::t('fe', ''),
            'kualitas_nyeri' => Yii::t('fe', ''),
            'faktor_pereda_nyeri' => Yii::t('fe', ''),
            'nutrisi_1a' => Yii::t('fe', ''),
            'nutrisi_1b' => Yii::t('fe', ''),
            'nutrisi_2' => Yii::t('fe', ''),
            'nilai_nutrisi' => Yii::t('fe', ''),
            'diagnosa_khusus' => Yii::t('fe', ''),
            'jenis_diagnosa_khusus' => Yii::t('fe', ''),
            'diagnosa_keperawatan' => Yii::t('fe', ''),
        ];
    }
}
