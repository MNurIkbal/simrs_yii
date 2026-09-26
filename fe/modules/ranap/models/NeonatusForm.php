<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class NeonatusForm extends Model
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

    public $usia_gestasi;
    public $riwayat_kesehatan;
    public $obat_lainnya;
    public $sakit_lainnya;
    public $rs_opname;
    public $keluhan_utama;
    public $riwayat_keluhan;
    public $riwayat_alergi;
    public $riwayat_alergi_lainnya;
    public $riwayat_alergi_reaksi;
    public $persepsi_ortu;
    public $harapan_ortu;
    public $riwayat_status;
    public $riwayat_status_lainnya;
    public $riwayat_status_anak;
    public $riwayat_status_ortu_berkunjung;
    public $riwayat_status_ortu_kontak_mata;
    public $riwayat_status_ortu_menyentuh;
    public $riwayat_status_ortu_berbicara;
    public $riwayat_status_ortu_menggendong;
    public $riwayat_status_ortu_perawat;
    public $kesadaran;
    public $gcs_e;
    public $gcs_m;
    public $gcs_v;
    public $ballard_score;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_badan;
    public $jalan_nafas;
    public $spontan;
    public $oksigen;
    public $saturasi;
    public $down_score;
    public $umur_kehamilan;
    public $keadaan_saat_ini;
    public $keadaan_saat_ini_sirkulasi;
    public $keadaan_saat_ini_elektrolit;
    public $alat_bantu_nafas;
    public $pemeriksaan_agd;
    public $asidosis;
    public $alkalalosis;
    public $keterangan;
    public $waktu_pengisian_kapiler;
    public $pengisian_kapiler;
    public $denyut_arten_kanan;
    public $denyut_arten_kiri;
    public $ekstremitas;
    public $perdarahan;
    public $uvc;
    public $intra_vena;
    public $intra_arteri;
    public $hasil_lab;
    public $keterangan_sirkulasi;
    public $umur_elektrolit;
    public $usia_gestasi_elektrolit;
    public $bb_lahir;
    public $bb_masuk;
    public $refleks_isap;
    public $refleks_telan;
    public $abdomen;
    public $cara_minum;
    public $lidah;
    public $lidah_lainnya;
    public $selaput_lendir;
    public $selaput_lendir_lainnya;
    public $turgor;
    public $hasil_lab_fisik;
    public $keterangan_fisik;
    public $tingkat_kesadaran;
    public $tangisan;
    public $kepala;
    public $ubun;
    public $pupil;
    public $gerakan;
    public $kejang;
    public $keterangan_sensori;
    public $warna_kulit;
    public $suhu_kulit;
    public $turgor_kulit;
    public $integritas_kulit;
    public $kepala_kulit;
    public $kuku_kulit;
    public $mata_kulit;
    public $sekret_kulit;
    public $tali_pusat_kulit;
    public $puntung_umbilikal_kulit;
    public $abdomen_kulit;
    public $keterangan_kulit;
    public $vagina;
    public $pseudo_menstruasi;
    public $kateter;
    public $preputium;
    public $hipospadia;
    public $labia;
    public $ambigus;
    public $keterangan_genital;
    public $frekuensi_bak;
    public $produksi_urine;
    public $keadaan_bak;
    public $alat_bantu_bak;
    public $alat_bantu_bak_lainnya;
    public $keterangan_bak;
    public $bab;
    public $keluar_mekonium;
    public $frekuensi_bab;
    public $konsistensi_faeces;
    public $keadaan_bab;
    public $gerakan_mobilisasi;
    public $keterangan_mobilisasi;
    public $keadaan_mobilisasi;
    public $pasien_lebih_banyak;
    public $keterangan_pasien;
    public $nyeri;
    public $onset;
    public $pencetus;
    public $lokasi_nyeri;
    public $gambaran_nyeri;
    public $durasi_nyeri;
    public $skala_nyeri;
    public $skoring_otot;
    public $skoring_menangis;
    public $skoring_pernafasan;
    public $skoring_lengan;
    public $skoring_kaki;
    public $skoring_kesadaran;
    public $skoring_skala_nyeri;
    public $frekuensi;
    public $kesediaan_keluarga;
    public $hambatan_keluarga_edukasi;
    public $paraf;
    public $hambatan_keluarga;
    public $hambatan_keluarga_lainnya;
    public $pendidikan_ortu;
    public $agama;
    public $agama_lainnya;
    public $penerjemah;
    public $penerjemah_lainnya;
    public $kebutuhan_edukasi;
    public $kebutuhan_edukasi_lainnya;
    public $jenis_pemeriksaan;
    public $asal_pemeriksaan;
    public $jumlah_pemeriksaan;
    public $diagnosa_keperawatan;
    public $rencana_asuhan_keperawatan;
    public $diagnosa_medis;
    public $vol_lainnya;
    public $kepala_lainnya;

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
                    'usia_gestasi',
                    'riwayat_kesehatan',
                    'obat_lainnya',
                    'sakit_lainnya',
                    'rs_opname',
                    'keluhan_utama',
                    'riwayat_keluhan',
                    'riwayat_alergi',
                    'riwayat_alergi_lainnya',
                    'riwayat_alergi_reaksi',
                    'persepsi_ortu',
                    'harapan_ortu',
                    'riwayat_status',
                    'riwayat_status_lainnya',
                    'riwayat_status_anak',
                    'riwayat_status_ortu_berkunjung',
                    'riwayat_status_ortu_kontak_mata',
                    'riwayat_status_ortu_menyentuh',
                    'riwayat_status_ortu_berbicara',
                    'riwayat_status_ortu_menggendong',
                    'riwayat_status_ortu_perawat',
                    'kesadaran',
                    'gcs_e',
                    'gcs_m',
                    'gcs_v',
                    'ballard_score',
                    'tekanan_darah',
                    'frekuensi_nadi',
                    'frekuensi_nafas',
                    'suhu_badan',
                    'jalan_nafas',
                    'spontan',
                    'oksigen',
                    'saturasi',
                    'down_score',
                    'umur_kehamilan',
                    'keadaan_saat_ini',
                    'keadaan_saat_ini_sirkulasi',
                    'keadaan_saat_ini_elektrolit',
                    'alat_bantu_nafas',
                    'pemeriksaan_agd',
                    'asidosis',
                    'alkalalosis',
                    'keterangan',
                    'waktu_pengisian_kapiler',
                    'pengisian_kapiler',
                    'denyut_arten_kanan',
                    'denyut_arten_kiri',
                    'ekstremitas',
                    'perdarahan',
                    'uvc',
                    'intra_vena',
                    'intra_arteri',
                    'hasil_lab',
                    'keterangan_sirkulasi',
                    'umur_elektrolit',
                    'usia_gestasi_elektrolit',
                    'bb_lahir',
                    'bb_masuk',
                    'refleks_isap',
                    'refleks_telan',
                    'abdomen',
                    'cara_minum',
                    'lidah',
                    'lidah_lainnya',
                    'selaput_lendir',
                    'selaput_lendir_lainnya',
                    'turgor',
                    'hasil_lab_fisik',
                    'keterangan_fisik',
                    'tingkat_kesadaran',
                    'tangisan',
                    'kepala',
                    'ubun',
                    'pupil',
                    'gerakan',
                    'kejang',
                    'keterangan_sensori',
                    'warna_kulit',
                    'suhu_kulit',
                    'turgor_kulit',
                    'integritas_kulit',
                    'kepala_kulit',
                    'kuku_kulit',
                    'mata_kulit',
                    'sekret_kulit',
                    'tali_pusat_kulit',
                    'puntung_umbilikal_kulit',
                    'abdomen_kulit',
                    'keterangan_kulit',
                    'vagina',
                    'pseudo_menstruasi',
                    'kateter',
                    'preputium',
                    'hipospadia',
                    'labia',
                    'ambigus',
                    'keterangan_genital',
                    'frekuensi_bak',
                    'produksi_urine',
                    'keadaan_bak',
                    'alat_bantu_bak',
                    'alat_bantu_bak_lainnya',
                    'keterangan_bak',
                    'bab',
                    'keluar_mekonium',
                    'frekuensi_bab',
                    'konsistensi_faeces',
                    'keadaan_bab',
                    'gerakan_mobilisasi',
                    'keterangan_mobilisasi',
                    'keadaan_mobilisasi',
                    'pasien_lebih_banyak',
                    'keterangan_pasien',
                    'nyeri',
                    'onset',
                    'pencetus',
                    'lokasi_nyeri',
                    'gambaran_nyeri',
                    'durasi_nyeri',
                    'skala_nyeri',
                    'skoring_otot',
                    'skoring_menangis',
                    'skoring_pernafasan',
                    'skoring_lengan',
                    'skoring_kaki',
                    'skoring_kesadaran',
                    'skoring_skala_nyeri',
                    'frekuensi',
                    'kesediaan_keluarga',
                    'hambatan_keluarga_edukasi',
                    'paraf',
                    'hambatan_keluarga',
                    'hambatan_keluarga_lainnya',
                    'pendidikan_ortu',
                    'agama',
                    'agama_lainnya',
                    'penerjemah',
                    'penerjemah_lainnya',
                    'kebutuhan_edukasi',
                    'kebutuhan_edukasi_lainnya',
                    'jenis_pemeriksaan',
                    'asal_pemeriksaan',
                    'jumlah_pemeriksaan',
                    'diagnosa_keperawatan',
                    'rencana_asuhan_keperawatan',
                    'diagnosa_medis',
                    'vol_lainnya',
                    'kepala_lainnya'
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
            'riwayat_keluhan' => 'Riwayat Keluhan Saat Ini',
            'rs_opname' => 'Di RS',
            'persepsi_ortu' => 'Persepsi Orang Tua Terhadap Pasien Saat Ini',
            'harapan_ortu' => 'Harapan Orang Tua Terhadap Pelayanan Keperawatan dan Pengobatan Saat Ini',
            'riwayat_status' => 'Biaya Perawatan',
            'riwayat_status_ortu_berkunjung' => 'Berkunjung',
            'riwayat_status_ortu_kontak_mata' => 'Kontak Mata',
            'riwayat_status_ortu_menyentuh' => 'Menyentuh',
            'riwayat_status_ortu_berbicara' => 'Berbicara',
            'riwayat_status_ortu_menggendong' => 'Menggendong',
            'riwayat_status_ortu_perawat' => 'Yang Akan Merawat Bayi di Rumah',
            'gcs_e' => 'GCS E',
            'gcs_m' => 'M',
            'gcs_v' => 'V',
            'denyut_arten_kanan' => 'Kanan',
            'denyut_arten_kiri' => 'Kiri',
            'uvc' => 'UVC (Umbilikal Vena Catheter)',
            'umur_elektrolit' => 'Umur',
            'usia_gestasi_elektrolit' => 'Usia Gestasi',
            'bb_lahir' => 'Berat Badan Lahir',
            'bb_masuk' => 'Berat Badan Masuk',
            'keadaan_saat_ini_sirkulasi' => 'Keadaan Saat Ini',
            'keadaan_saat_ini_elektrolit' => 'Keadaan Saat Ini',
            'saran_nasehat_dokter' => 'Saran/Nasehat Dokter',
            'ubun' => 'Ubun-Ubun',
            'hasil_lab_fisik' => 'Hasil Laboratorium',
            'keterangan_sirkulasi' => 'Keterangan',
            'keterangan_fisik' => 'Keterangan',
            'keterangan_sensori' => 'Keterangan',
            'warna_kulit' => 'Warna',
            'suhu_kulit' => 'Suhu',
            'turgor_kulit' => 'Turgor',
            'integritas_kulit' => 'Integritas',
            'kepala_kulit' => 'Kepala',
            'kuku_kulit' => 'Kuku',
            'mata_kulit' => 'Mata',
            'sekret_kulit' => 'Sekret',
            'tali_pusat_kulit' => 'Tali Pusat',
            'puntung_umbilikal_kulit' => 'Puntung Umbikal',
            'abdomen_kulit' => 'Abdomen',
            'keterangan_kulit' => 'Keterangan',
            'labia' => 'Labia = Prominem', 
            'keterangan_genital' => 'Keterangan',
            'frekuensi_bak' => 'Frekuensi BAK',
            'keadaan_bak' => 'Keadaan Saat Ini',
            'alat_bantu_bak' => 'Alat Bantu yang digunakan',
            'keterangan_bak' => 'Keterangan',
            'frekuensi_bab' => 'Frekuensi BAB',
            'keadaan_bab' => 'Keadaan Saat Ini',
            'gerakan_mobilisasi' => 'Gerakan',
            'keterangan_mobilisasi' => 'Keterangan',
            'keadaan_mobilisasi' => 'Keadaan Saat Ini',
            'keterangan_pasien' => 'Keterangan',
            'bab' => 'Buang Air Besar (BAB)',
            'skoring_otot' => 'Otot',
            'skoring_menangis' => 'Menangis',
            'skoring_pernafasan' => 'Pola Pernafasan',
            'skoring_lengan' => 'Lengan',
            'skoring_kaki' => 'Kaki',
            'skoring_kesadaran' => 'Keadaan/Kesadaran',
            'skoring_skala_nyeri' => 'Skala Nyeri',
            'kesediaan_keluarga' => 'a. Kesediaan keluarga menerima informasi',
            'hambatan_keluarga_edukasi' => 'b. Terdapat hambatan dalam edukasi',
            'hambatan_keluarga' => 'Jika Ya, sebutkan hambatannya (bisa di ceklis lebih dari satu)',
            'pendidikan_ortu' => 'Tingkat pendidikan orang tua',
            'agama' => 'Agama dan nilai kepercayaan orang tua',
            'penerjemah' => 'Dibutuhkan Penerjemah',
            'kebutuhan_edukasi' => 'Kebutuhan edukasi (pilih topik edukasi pada kotak yang tersedia)',
            'penerjemah_lainnya' => 'Sebutkan',
            'diagnosa_keperawatan' => 'Masalah/Diagnosa Keperawatan',
            'rencana_asuhan_keperawatan' => 'Rencana Asuhan Keperawatan'
        ];
    }
}
