<?php

namespace app\modules\igd\models;

use Yii;

class AsesmenKeperawatanIgdForm extends \yii\base\Model
{
    public $asesmenperawatrd_id;
    public $pendaftaran_id;
    public $ruangan_id;
    public $tgl_asesmen;
    public $perawat_id;
    public $tgl_pendaftaran;
    public $tgl_datang;
    public $prioritas_triage;
    public $is_trauma;
    public $pasien_datang;
    public $jenis_asmenperawat;
    public $alasan_kunjungan;
    public $is_alergi;
    public $is_alergiobat;
    public $alergi_obat;
    public $is_alergilainnya;
    public $alergi_lainnya;
    public $keadaan_umum;
    public $is_nyeri;
    public $pilih_skala;
    public $lokasi_nyeri;
    public $skala_nyeri;
    public $metode_nyeri;
    public $is_resikojatuh;
    public $keluhan;
    public $lama_sakit;
    public $r_penyakitkeluarga;
    public $catatan_asesmen;
    public $gcseye_id;
    public $gcsverbal_id;
    public $gcsmotorik_id;
    public $hasil_gcs;
    public $is_kapitis;
    public $td_systolic;
    public $td_diastolic;
    public $tekanan_darah;
    public $hasil_td;
    public $detak_nadi;
    public $pernapasan;
    public $suhu_tubuh;
    public $tinggi_badan;
    public $berat_badan;
    public $bb_ideal;
    public $imt;
    public $ket_imt;
    public $spo2;
    public $kelaianan_tubuh;
    public $detail_asesmen;
    public $formulir_triage;
    public $formulir_fisik;
    public $tgl_keluar;
    public $agama_id;
    public $pekerjaan_id;
    public $caramasuk_id;
    public $pendidikan_id;
    public $jalur_nafas;
    public $jalur_nafas_oksigen;
    public $pernafasan;
    public $pernafasan_spontan;
    public $pernafasan_takipnea;
    public $pernafasan_gargling;
    public $tensi;
    public $tipe_nadi;
    public $hasil_nadi;
    public $reguler;
    public $ireguler;
    public $ireguler_tipe;
    public $capilary_refill;
    public $perfusi;
    public $akral;
    public $pendarahan;
    public $pendarahan_cc;
    public $is_bradikarida;
    public $is_takikardia;
    public $pupil;
    public $reaksi_pupil;
    public $reaksi_pupil_lainnya;
    public $kepala;
    public $abdomen;
    public $maksilofacial;
    public $parineum;
    public $tulan_leher;
    public $muskuloskeletal;
    public $paru_paru;
    public $extremitas;
    public $skala_wong_baker;
    public $r_penyakitdahulu;
    public $r_penyakitsaatini;
    public $r_pengobatan;
    public $jalan_nafas_bersin;
    public $asesmen_auto;
    public $asesmen_allo;
    public $asesmen_auto_anamnesa;
    public $asesmen_allo_text;
    public $nilai_decubitus;
    public $nilai_luka_bakar;
    public $survey_kepala;
    public $survey_kepala_lacerasi;
    public $survey_kepala_battle_sign;
    public $survey_kepala_lainnya;
    public $survey_mata;
    public $survey_mulut;
    public $survey_mulut_luka_dalam;
    public $survey_mulut_lainnya;
    public $survey_telinga;
    public $survey_leher;
    public $survey_leher_lainnya;
    public $survey_extremitas;
    public $survey_extremitas_pulsasi;
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
    public $perasaan_klien;
    public $sosial_support;
    public $hubungan_pasien;
    public $keluarga_lain;
    public $keadaan_emosi;
    public $suku_id;
    public $bahasa_dipakai;
    public $bahasa_dipakai_lainnya;
    public $is_penerjemah;
    public $media;
    public $media_lainnya;
    public $identifikasi_hambatan;
    public $is_sistem_rujukan;
    public $materi;
    public $edukator;
    public $is_ketersediaan_pasien;
    public $is_kemampuan_membaca;
    public $bahasa;
    public $is_dibutuhkan_penerjemah;
    public $penerjemah_bahasa;
    public $is_hambatan_emotional;
    public $hambatan_emotional_lainnya;
    public $is_keterbatasan_fisik;
    public $keterbatasan_fisik;
    public $bersihan_jalan;
    public $pola_nafas;
    public $gangguan_gas;
    public $nyeri;
    public $penurunan_jantung;
    public $gangguan_perfusi_cerebral;
    public $gangguan_perfusi_perifer;
    public $valume_cairan_tubuh;
    public $gangguan_thermoregulasi_tipe;
    public $gangguan_thermoregulasi_nilai;
    public $tujuan_pulang;
    public $tujuan_pulang_lainnya;
    public $transportasi;
    public $orang_merawat;
    public $sarana_kesehatan;
    public $masuk_ke;
    public $survey_kepala_utuh;
    public $persen_luka_bakar;
    public $kategori_triase_disaster;
    public $kategori_triase_sehari;
    public $hasil_resiko_jatuh;
    public $jenis_resiko_jatuh;
    public $nutrisi_1a;
    public $nutrisi_1b;
    public $nutrisi_2;
    public $diagnosa_khusus;
    public $jenis_diagnosa_khusus;
    public $strongkids_kurus;
    public $strongkids_turunbb;
    public $strongkids_kondisikhusus;
    public $strongkids_keadaan_beresiko;
    public $resusitasi;
    public $extremitas_utuh;
    public $extremitas_fraktur;
    public $extremitas_nyeri;
    public $extremitas_deformitas;
    public $extremitas_defisit_neurologis;
    public $extremitas_jejas;
    public $extremitas_pulsasi;
    public $pupil_os;
    public $pupil_od;
    public $diagnosa_keperawatan;
    public $survey_kepala_tidak_ada_kelainan;
    public $survey_mulut_tidak_ada_kelainan;
    public $extremitas_tidak_ada_kelainan;
    public $survey_dada_tidak_ada_kelainan;
    public $survey_abdomen_tidak_ada_kelainan;
    public $is_verifikasigizi;
    public $pegawaiverifikasigizi_id;
    public $pegawaiverifikasigizi_nama;
    public $tgl_verifikasigizi;
    public $allo_or_auto;

    public function rules()
    {
        return [
            [
                [
                    'asesmenperawatrd_id',
                    'pendaftaran_id',
                    'ruangan_id',
                    'tgl_asesmen',
                    'perawat_id',
                    'tgl_pendaftaran',
                    'tgl_datang',
                    'prioritas_triage',
                    'is_trauma',
                    'pasien_datang',
                    'jenis_asmenperawat',
                    'alasan_kunjungan',
                    'is_alergi',
                    'is_alergiobat',
                    'alergi_obat',
                    'is_alergilainnya',
                    'alergi_lainnya',
                    'keadaan_umum',
                    'is_nyeri',
                    'pilih_skala',
                    'lokasi_nyeri',
                    'skala_nyeri',
                    'metode_nyeri',
                    'is_resikojatuh',
                    'keluhan',
                    'lama_sakit',
                    'r_penyakitdahulu',
                    'r_penyakitkeluarga',
                    'catatan_asesmen',
                    'gcseye_id',
                    'gcsverbal_id',
                    'gcsmotorik_id',
                    'hasil_gcs',
                    'is_kapitis',
                    'td_systolic',
                    'td_diastolic',
                    'tekanan_darah',
                    'hasil_td',
                    'detak_nadi',
                    'pernapasan',
                    'suhu_tubuh',
                    'tinggi_badan',
                    'berat_badan',
                    'bb_ideal',
                    'imt',
                    'ket_imt',
                    'spo2',
                    'kelaianan_tubuh',
                    'detail_asesmen',
                    'formulir_triage',
                    'formulir_fisik',
                    'tgl_keluar',
                    'agama_id',
                    'pekerjaan_id',
                    'caramasuk_id',
                    'pendidikan_id',
                    'jalur_nafas',
                    'jalur_nafas_oksigen',
                    'pernafasan',
                    'pernafasan_spontan',
                    'pernafasan_takipnea',
                    'pernafasan_gargling',
                    'tensi',
                    'tipe_nadi',
                    'hasil_nadi',
                    'reguler',
                    'ireguler',
                    'ireguler_tipe',
                    'capilary_refill',
                    'perfusi',
                    'akral',
                    'pendarahan',
                    'pendarahan_cc',
                    'is_bradikarida',
                    'is_takikardia',
                    'pupil',
                    'reaksi_pupil',
                    'reaksi_pupil_lainnya',
                    'kepala',
                    'abdomen',
                    'maksilofacial',
                    'parineum',
                    'tulan_leher',
                    'muskuloskeletal',
                    'paru_paru',
                    'extremitas',
                    'skala_wong_baker',
                    'r_penyakitdahulu',
                    'r_penyakitsaatini',
                    'r_pengobatan',
                    'jalan_nafas_bersin',
                    'asesmen_auto',
                    'asesmen_allo',
                    'nilai_decubitus',
                    'nilai_luka_bakar',
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
                    'perasaan_klien',
                    'sosial_support',
                    'hubungan_pasien',
                    'keluarga_lain',
                    'keadaan_emosi',
                    'suku_id',
                    'bahasa_dipakai',
                    'bahasa_dipakai_lainnya',
                    'is_penerjemah',
                    'media',
                    'media_lainnya',
                    'identifikasi_hambatan',
                    'is_sistem_rujukan',
                    'materi',
                    'edukator',
                    'is_ketersediaan_pasien',
                    'is_kemampuan_membaca',
                    'bahasa',
                    'is_dibutuhkan_penerjemah',
                    'penerjemah_bahasa',
                    'is_hambatan_emotional',
                    'hambatan_emotional_lainnya',
                    'is_keterbatasan_fisik',
                    'keterbatasan_fisik',
                    'bersihan_jalan',
                    'pola_nafas',
                    'gangguan_gas',
                    'nyeri',
                    'penurunan_jantung',
                    'gangguan_perfusi_cerebral',
                    'gangguan_perfusi_perifer',
                    'valume_cairan_tubuh',
                    'gangguan_thermoregulasi_tipe',
                    'gangguan_thermoregulasi_nilai',
                    'tujuan_pulang',
                    'tujuan_pulang_lainnya',
                    'transportasi',
                    'orang_merawat',
                    'sarana_kesehatan',
                    'masuk_ke',
                    'survey_kepala_utuh',
                    'persen_luka_bakar',
                    'kategori_triase_disaster',
                    'kategori_triase_sehari',
                    'hasil_resiko_jatuh',
                    'jenis_resiko_jatuh',
                    'nutrisi_1a',
                    'nutrisi_1b',
                    'nutrisi_2',
                    'diagnosa_khusus',
                    'jenis_diagnosa_khusus',
                    'strongkids_kurus',
                    'strongkids_turunbb',
                    'strongkids_kondisikhusus',
                    'strongkids_keadaan_beresiko',
                    'resusitasi',
                    'extremitas_utuh',
                    'extremitas_fraktur',
                    'extremitas_nyeri',
                    'extremitas_deformitas',
                    'extremitas_defisit_neurologis',
                    'extremitas_jejas',
                    'extremitas_pulsasi',
                    'pupil_os',
                    'pupil_od',
                    'diagnosa_keperawatan',
                    'asesmen_auto_anamnesa',
                    'asesmen_allo_text',
                    'survey_kepala_tidak_ada_kelainan',
                    'survey_mulut_tidak_ada_kelainan',
                    'survey_dada_tidak_ada_kelainan',
                    'survey_abdomen_tidak_ada_kelainan',
                    'is_verifikasigizi',
                    'pegawaiverifikasigizi_id',
                    'pegawaiverifikasigizi_nama',
                    'tgl_verifikasigizi',
                    'allo_or_auto'
                ],
                'safe'
            ],
            [
                [
                    'tinggi_badan',
                    'berat_badan',
                    'keluhan',
                    'r_penyakitsaatini',
                    'r_penyakitdahulu',
                    'is_alergi',
                    'r_pengobatan',
                ],
                'required'
            ],
            [
                'asesmen_auto', 'required', 'when' => function ($model) {
                    return empty($model->asesmen_allo) ? true : false;
                }
            ],
            [
                'asesmen_allo', 'required', 'when' => function ($model) {
                    return empty($model->asesmen_auto) ? true : false;
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_datang' => 'Tanggal Datang',
            'tgl_keluar' => 'Tanggal Keluar',
            'pendaftaran_id' => 'pendaftaran_id',
            'ruangan_id' => 'ruangan_id',
            'tgl_asesmen' => 'tgl_asesmen',
            'perawat_id' => 'perawat_id',
            'tgl_pendaftaran' => 'tgl_pendaftaran',
            'prioritas_triage' => 'prioritas_triage',
            'is_trauma' => 'is_trauma',
            'pasien_datang' => 'pasien_datang',
            'jenis_asmenperawat' => 'jenis_asmenperawat',
            'alasan_kunjungan' => 'alasan_kunjungan',
            'is_alergi' => 'Riwayat Alergi',
            'is_alergiobat' => 'is_alergiobat',
            'alergi_obat' => 'alergi_obat',
            'is_alergilainnya' => 'is_alergilainnya',
            'alergi_lainnya' => 'alergi_lainnya',
            'keadaan_umum' => 'keadaan_umum',
            'is_nyeri' => 'is_nyeri',
            'pilih_skala' => 'pilih_skala',
            'lokasi_nyeri' => 'lokasi_nyeri',
            'skala_nyeri' => 'skala_nyeri',
            'metode_nyeri' => 'metode_nyeri',
            'is_resikojatuh' => 'is_resikojatuh',
            'keluhan' => 'Keluhan Utama',
            'lama_sakit' => 'lama_sakit',
            'r_penyakitdahulu' => 'Riwayat Penyakit Dahulu',
            'r_penyakitkeluarga' => 'Riwayat Penyakit Keluargas',
            'catatan_asesmen' => 'catatan_asesmen',
            'gcseye_id' => 'GCS Eye',
            'gcsverbal_id' => 'GCS Verbal',
            'gcsmotorik_id' => 'GCS Motorik',
            'hasil_gcs' => 'Hasil Metode GCS',
            'is_kapitis' => 'Kapitis',
            'td_systolic' => 'td_systolic',
            'td_diastolic' => 'td_diastolic',
            'tekanan_darah' => 'tekanan_darah',
            'hasil_td' => 'hasil_td',
            'detak_nadi' => 'Nadi',
            'pernapasan' => 'pernapasan',
            'suhu_tubuh' => 'Suhu',
            'tinggi_badan' => 'Tinggi Badan',
            'berat_badan' => 'Berat Badan',
            'bb_ideal' => 'Berat Badan Ideal',
            'imt' => 'IMT',
            'ket_imt' => 'Klasifikasi Berat Badan',
            'spo2' => 'spo2',
            'kelaianan_tubuh' => 'kelaianan_tubuh',
            'detail_asesmen' => 'detail_asesmen',
            'formulir_triage' => 'formulir_triage',
            'formulir_fisik' => 'formulir_fisik',
            'agama_id' => 'Agama',
            'pekerjaan_id' => 'Pekerjaan',
            'caramasuk_id' => 'Cara Datang',
            'pendidikan_id' => 'Pendidikan',
            'jalur_nafas' => 'jalur_nafas',
            'jalur_nafas_oksigen' => 'jalur_nafas_oksigen',
            'pernafasan' => 'pernafasan',
            'pernafasan_spontan' => 'pernafasan_spontan',
            'pernafasan_takipnea' => 'Pernafasan Takipnea',
            'pernafasan_gargling' => 'Pernafasan Takipnea',
            'tensi' => 'Tensi',
            'tipe_nadi' => 'Tipe Nadi',
            'hasil_nadi' => 'Hasil Nadi',
            'reguler' => 'Reguler',
            'ireguler' => 'Ireguler',
            'ireguler_tipe' => 'Ireguler Tipe',
            'capilary_refill' => 'capilary_refill',
            'perfusi' => 'Perfusi',
            'akral' => 'akral',
            'pendarahan' => 'pendarahan',
            'pendarahan_cc' => 'pendarahan_cc',
            'is_bradikarida' => 'is_bradikarida',
            'is_takikardia' => 'is_takikardia',
            'pupil' => 'Pupil',
            'reaksi_pupil' => 'Reaksi Pupil',
            'reaksi_pupil_lainnya' => 'Reaksi Pupil Lainnya',
            'kepala' => 'Kepala',
            'abdomen' => 'Abdomen',
            'maksilofacial' => 'Maksilofacial',
            'parineum' => 'Parineum',
            'tulan_leher' => 'Tulang Leher',
            'muskuloskeletal' => 'Muskuloskeletal',
            'paru_paru' => 'Paru-Paru',
            'extremitas' => 'Extremitas',
            'skala_wong_baker' => 'skala_wong_baker',
            'r_penyakitdahulu' => 'Riwayat Penyakit',
            'r_penyakitsaatini' => 'Riwayat Penyakit Saat ini',
            'r_pengobatan' => 'Riwayat Pengobatan',
            'jalan_nafas_bersin' => 'Sumbatan',
            'asesmen_auto' => 'Asesmen Keperawatan',
            'asesmen_allo' => 'Asesmen Keperawatan',
            'nilai_decubitus' => 'Score Decubitus',
            'nilai_luka_bakar' => 'Nilai Luka Bakar',
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
            'perasaan_klien' => 'perasaan_klien',
            'sosial_support' => 'sosial_support',
            'hubungan_pasien' => 'hubungan_pasien',
            'keluarga_lain' => 'keluarga_lain',
            'keadaan_emosi' => 'keadaan_emosi',
            'suku_id' => 'Kultural',
            'bahasa_dipakai' => 'bahasa_dipakai',
            'bahasa_dipakai_lainnya' => 'bahasa_dipakai_lainnya',
            'is_penerjemah' => 'is_penerjemah',
            'media' => 'media',
            'media_lainnya' => 'media_lainnya',
            'identifikasi_hambatan' => 'identifikasi_hambatan',
            'is_sistem_rujukan' => 'is_sistem_rujukan',
            'materi' => 'Materi',
            'edukator' => 'Edukator',
            'is_ketersediaan_pasien' => 'is_ketersediaan_pasien',
            'is_kemampuan_membaca' => 'is_kemampuan_membaca',
            'bahasa' => 'Bahasa',
            'is_dibutuhkan_penerjemah' => 'is_dibutuhkan_penerjemah',
            'penerjemah_bahasa' => 'penerjemah_bahasa',
            'is_hambatan_emotional' => 'is_hambatan_emotional',
            'hambatan_emotional_lainnya' => 'hambatan_emotional_lainnya',
            'is_keterbatasan_fisik' => 'is_keterbatasan_fisik',
            'keterbatasan_fisik' => 'keterbatasan_fisik',
            'bersihan_jalan' => 'Bersihan Jalan tidak efektif',
            'pola_nafas' => 'Pola napas tidak efektif',
            'gangguan_gas' => 'Gangguan pertukaran gas',
            'nyeri' => 'Nyeri',
            'penurunan_jantung' => 'Penurunan curah Jantung',
            'gangguan_perfusi_cerebral' => 'Gangguan Perfusi Cerebral',
            'gangguan_perfusi_perifer' => 'Gangguan perfusi jaringan perifer',
            'valume_cairan_tubuh' => 'Volume cairan tubuh kurang dari kebutuhan tubuh',
            'gangguan_thermoregulasi_tipe' => 'gangguan_thermoregulasi_tipe',
            'gangguan_thermoregulasi_nilai' => 'gangguan_thermoregulasi_nilai',
            'tujuan_pulang' => 'tujuan_pulang',
            'tujuan_pulang_lainnya' => 'tujuan_pulang_lainnya',
            'transportasi' => 'transportasi',
            'orang_merawat' => 'orang_merawat',
            'sarana_kesehatan' => 'sarana_kesehatan',
            'masuk_ke' => 'masuk_ke',
            'survey_kepala_utuh' => 'survey_kepala_utuh',
            'persen_luka_bakar' => 'persen_luka_bakar',
            'kategori_triase_disaster' => 'Kategori triase disaster',
            'kategori_triase_sehari' => 'Kategori triase sehari',
            'hasil_resiko_jatuh' => 'Resiko Jatuh',
            'jenis_resiko_jatuh' => 'Jenis Resiko Jatuh',
            'allo_or_auto' => 'allo_or_auto',
        ];
    }
}
