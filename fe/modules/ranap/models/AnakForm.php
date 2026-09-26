<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class AnakForm extends Model
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
    public $gcs;
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
    public $usia_kehamilan;
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


    public $pb_lahir;
    public $menangis;
    public $persalinan;
    public $riwayat_kuning;
    public $riwayat_imunisasi;
    public $riwayat_imunisasi_lainnya;
    
    public $tengkurep_usia;
    public $tumbuh_gigi_usia;
    public $bicara_usia;
    public $duduk_usia;
    public $berdiri_usia;
    public $berjalan_usia;
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
    public $kesulitan_komunikasi;
    public $kebiasaan_diet;
    public $kebiasaan_diet_lainnya;
    public $kebiasaan_beribadah_kultural;
    public $kepercayaan;
    public $kepercayaan_lainnya;
    public $status_demografi;
    public $pantangan_berobat;
    public $pantangan_berobat_lainnya;
    public $berat_badan;
    public $tinggi_badan;
    public $irama;
    public $kedalaman;
    public $pola_nafas;
    public $batuk;
    public $bentuk_dada;
    public $ekspansi_dada;
    public $otot_pernafasan;
    public $trauma;
    public $lain_lain;
    public $sputum;
    public $sputum_warna;
    public $clubbing_finge;
    public $trakhea;
    public $pembesaran_kelenjar;
    public $perkusi;
    public $auskultasi;
    public $sianosis;
    public $pucat;
    public $akral;
    public $lain_lain_kardiovaskuler;
    public $irama_jantung;
    public $vena_kanan;
    public $vena_kiri;
    public $mulut;
    public $gigi_palsu;
    public $mual;
    public $muntah;
    public $bising_usus;
    public $asites;
    public $pembesaran_hepar;
    public $pembesaran_limfa;
    public $lingkar_perut;
    public $panjang_lingkar_perut;
    public $pendengaran;
    public $pendengaran_lainnya;
    public $penglihatan;
    public $penglihatan_lainnya;
    public $penciuman;
    public $penciuman_lainnya;
    public $pupil_isokor;
    public $lain_lain_neurosensori;
    public $defekasi;
    public $konsistensi;
    public $urinal;
    public $kelainan;
    public $kelainan_lainnya;
    public $pola_urine;
    public $masalah_urine;
    public $frekuensi_urine;
    public $terdapat_luka;
    public $lesi_primer;
    public $lesi_sekunder;
    public $lokasi_luka;
    public $kondisi_kuku;
    public $genitalia_laki;
    public $genitalia_perempuan;
    public $kesulitan_pergerakan;
    public $ukuran_otot;
    public $gerakan_otot;
    public $keadaan_otot;
    public $keadaan_tulang;
    public $keadaan_sendi;
    public $lain_lain_muskuloskeletal;
    public $lama_tidur;
    public $kesulitan_tidur;
    public $kesulitan_tidur_lainnya;
    public $rambut;
    public $badan;
    public $gigi_mulut;
    public $keadaan_kuku;
    public $skor_wajah;
    public $skor_kaki;
    public $skor_kegiatan;
    public $skor_menangis;
    public $skor_konsol;
    public $total_skor;
    public $rasa_nyeri;
    public $frekuensi_nyeri;
    public $tipe_nyeri;
    public $karakteristik_nyeri;
    public $karakteristik_nyeri_lainnya;
    public $pengaruh_nyeri;
    public $pengaruh_nyeri_lainnya;
    public $pereda_nyeri;
    public $pereda_nyeri_lainnya;
    public $nyeri_dirasakan;
    public $nyeri_dirasakan_lainnya;
    public $nyeri_ringan;
    public $nyeri_sedang;
    public $nyeri_berat;
    public $total_skor_nyeri;
    public $parameter_umur;
    public $parameter_gender;
    public $parameter_diagnosa;
    public $parameter_kognitif;
    public $parameter_lingkungan;
    public $parameter_respon;
    public $parameter_obat;
    public $total_skor_parameter;
    public $skor_kondisi_fisik;
    public $skor_status_mental;
    public $skor_aktifitas;
    public $skor_mobilitas;
    public $skor_inkontinensia;
    public $skor_defekasi;
    public $skor_berkemih;
    public $skor_bersih;
    public $skrining_penyakit;
    public $skrining_kurus;
    public $skrining_diare;
    public $skrining_bb;
    public $hambatan_pembelajaran;
    public $hambatan_pembelajaran_lainnya;
    public $edukasi;
    public $edukasi_lainnya;
    public $risiko_hc;
    public $risiko_implan;
    public $risiko_alat;
    public $risiko_terapis;
    public $risiko_gizi;
    public $risiko_perawatan;
    public $risiko_lain;
    public $keterangan_hc;
    public $keterangan_implan;
    public $keterangan_alat;
    public $keterangan_terapis;
    public $keterangan_gizi;
    public $keterangan_perawatan;
    public $keterangan_lain;

    public $makanan;
    public $asi;
    public $asi_lamanya;
    public $susu_formula;
    public $kategori_dekubitus;
    public $perkiraan_lama_rawat;
    public $perkiraan_tanggal_pulang;
    public $kategori_status_fungsional;
    public $kategori_skor_modifikasi;
    public $kategori_risiko_jatuh;
    public $sumber_data;
    public $sumber_data_lainnya;

    public $skor_jamban;
    public $skor_makan;
    public $skor_duduk;
    public $skor_berjalan;
    public $skor_baju;
    public $skor_tangga;
    public $skor_mandi;
    public $kategori_tingkat_nyeri;
    public $kategori_umur;
    public $kategori_nyeri;
    public $skor_nyeri;

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
                    'gcs',
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
                    'usia_kehamilan',
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
                    'kepala_lainnya',
                    'pb_lahir',
                    'menangis',
                    'persalinan',
                    'riwayat_kuning',
                    'riwayat_imunisasi',
                    'riwayat_imunisasi_lainnya',
                    'tengkurep_usia',
                    'tumbuh_gigi_usia',
                    'bicara_usia',
                    'duduk_usia',
                    'berdiri_usia',
                    'berjalan_usia',
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
                    'kesulitan_komunikasi',
                    'kebiasaan_diet',
                    'kebiasaan_diet_lainnya',
                    'kebiasaan_beribadah_kultural',
                    'kepercayaan',
                    'kepercayaan_lainnya',
                    'status_demografi',
                    'pantangan_berobat',
                    'pantangan_berobat_lainnya',
                    'berat_badan',
                    'tinggi_badan',
                    'irama',
                    'kedalaman',
                    'pola_nafas',
                    'batuk',
                    'bentuk_dada',
                    'ekspansi_dada',
                    'otot_pernafasan',
                    'trauma',
                    'lain_lain',
                    'sputum',
                    'sputum_warna',
                    'clubbing_finge',
                    'trakhea',
                    'pembesaran_kelenjar',
                    'perkusi',
                    'auskultasi',
                    'sianosis',
                    'pucat',
                    'akral',
                    'lain_lain_kardiovaskuler',
                    'irama_jantung',
                    'vena_kanan',
                    'vena_kiri',
                    'mulut',
                    'gigi_palsu',
                    'mual',
                    'muntah',
                    'bising_usus',
                    'asites',
                    'pembesaran_hepar',
                    'pembesaran_limfa',
                    'lingkar_perut',
                    'panjang_lingkar_perut',
                    'pendengaran',
                    'pendengaran_lainnya',
                    'penglihatan',
                    'penglihatan_lainnya',
                    'penciuman',
                    'penciuman_lainnya',
                    'pupil_isokor',
                    'lain_lain_neurosensori',
                    'defekasi',
                    'konsistensi',
                    'urinal',
                    'kelainan',
                    'kelainan_lainnya',
                    'pola_urine',
                    'masalah_urine',
                    'frekuensi_urine',
                    'terdapat_luka',
                    'lesi_primer',
                    'lesi_sekunder',
                    'lokasi_luka',
                    'kondisi_kuku',
                    'genitalia_laki',
                    'genitalia_perempuan',
                    'kesulitan_pergerakan',
                    'ukuran_otot',
                    'gerakan_otot',
                    'keadaan_otot',
                    'keadaan_tulang',
                    'keadaan_sendi',
                    'lain_lain_muskuloskeletal',
                    'lama_tidur',
                    'kesulitan_tidur',
                    'kesulitan_tidur_lainnya',
                    'rambut',
                    'badan',
                    'gigi_mulut',
                    'keadaan_kuku',
                    'skor_wajah',
                    'skor_kaki',
                    'skor_kegiatan',
                    'skor_menangis',
                    'skor_konsol',
                    'total_skor',
                    'rasa_nyeri',
                    'frekuensi_nyeri',
                    'tipe_nyeri',
                    'karakteristik_nyeri',
                    'karakteristik_nyeri_lainnya',
                    'pengaruh_nyeri',
                    'pengaruh_nyeri_lainnya',
                    'pereda_nyeri',
                    'pereda_nyeri_lainnya',
                    'nyeri_dirasakan',
                    'nyeri_dirasakan_lainnya',
                    'nyeri_ringan',
                    'nyeri_sedang',
                    'nyeri_berat',
                    'total_skor_nyeri',
                    'parameter_umur',
                    'parameter_gender',
                    'parameter_diagnosa',
                    'parameter_kognitif',
                    'parameter_lingkungan',
                    'parameter_respon',
                    'parameter_obat',
                    'total_skor_parameter',
                    'skor_kondisi_fisik',
                    'skor_status_mental',
                    'skor_aktifitas',
                    'skor_mobilitas',
                    'skor_inkontinensia',
                    'skor_defekasi',
                    'skor_berkemih',
                    'skor_bersih',
                    'skrining_penyakit',
                    'skrining_kurus',
                    'skrining_diare',
                    'skrining_bb',
                    'hambatan_pembelajaran',
                    'hambatan_pembelajaran_lainnya',
                    'edukasi',
                    'edukasi_lainnya',
                    'risiko_hc',
                    'risiko_implan',
                    'risiko_alat',
                    'risiko_terapis',
                    'risiko_gizi',
                    'risiko_perawatan',
                    'risiko_lain',
                    'keterangan_hc',
                    'keterangan_implan',
                    'keterangan_alat',
                    'keterangan_terapis',
                    'keterangan_gizi',
                    'keterangan_perawatan',
                    'keterangan_lain',
                    'makanan',
                    'asi',
                    'asi_lamanya',
                    'susu_formula',
                    'kategori_dekubitus',
                    'perkiraan_lama_rawat',
                    'perkiraan_tanggal_pulang',
                    'kategori_status_fungsional',
                    'kategori_skor_modifikasi',
                    'kategori_risiko_jatuh',
                    'sumber_data',
                    'sumber_data_lainnya',
                    'skor_jamban',
                    'skor_makan',
                    'skor_duduk',
                    'skor_berjalan',
                    'skor_baju',
                    'skor_tangga',
                    'skor_mandi',
                    'kategori_tingkat_nyeri',
                    'kategori_umur',
                    'kategori_nyeri',
                    'skor_nyeri',
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
            'warna_kulit' => 'Warna Kulit',
            'suhu_kulit' => 'Suhu',
            'turgor_kulit' => 'Turgor Kulit',
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
            'rencana_asuhan_keperawatan' => 'Rencana Asuhan Keperawatan',
            'pb_lahir' => 'Panjang Badan Lahir',

        ];
    }
}
