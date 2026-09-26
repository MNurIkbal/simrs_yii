<?php

namespace app\modules\ranap\models;

use Yii;

class AsesmenMedisForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $asesmenmedis_id;
    public $pasienadmisi_id;
    public $dokter_id;
    public $keluhan_utama;
    public $keluhan_tambahan;
    public $r_penyakitsekarang;
    public $lama_sakit;
    public $r_penyakitdahulu;
    public $r_penyakitkeluarga;
    public $r_imunisasi;
    public $r_peskk;
    public $tgl_asesmenmedis;
    public $sumber_info;
    public $sumber_info_lainnya;
    public $sumber_hubungan;
    public $is_merokok;
    public $jumlah_rokok;
    public $obat_diberikan;
    public $r_makanan;
    public $r_kelahiran;
    public $r_alergiobat;
    public $keterangan;
    public $td_systolic;
    public $td_diastolic;
    public $tekanan_darah;
    public $hasil_td;
    public $tinggi_badan;
    public $berat_badan;
    public $bb_ideal;
    public $imt;
    public $ket_imt;
    public $detak_nadi;
    public $pernapasan;
    public $denyut_jantung; 
    public $suhu_tubuh;
    public $gcs_eye;
    public $gcs_verbal;
    public $gcs_motorik;
    public $jumlah_gcs;
    public $is_kapitis;
    public $hasil_gcs;
    public $kontak;
    public $metod_asmennyeri;
    public $is_terintubasi;
    public $skala;
    public $lokasi_nyeri;
    public $inspeksi_kepala;
    public $palpasi_kepala;
    public $neurologi_kepala;
    public $is_kakududuk;
    public $jvp;
    public $kgb;
    public $inspeksi_toraks;
    public $palpasi_toraks;
    public $perkusi_toraks;
    public $auskultasi_toraks;
    public $inspeksi_punggung;
    public $palpasi_punggung;
    public $perkusi_punggung;
    public $auskultasi_punggung;
    public $inspeksi_abdomen;
    public $palpasi_abdomen;
    public $perkusi_abdomen;
    public $auskultasi_abdomen;
    public $hepar;
    public $lien;
    public $inspeksi_ekstrim;
    public $palpasi_ekstrim;
    public $neurologi_ekstrim;
    public $anus_genitalia;
    public $catatan_rad;
    public $catatan_lab;
    public $diagnosa_id;
    public $masalah;
    public $discharge_plan;
    public $care_plan;
    public $loged = false;
    public $kepala;
    public $mulut;
    public $mata;
    public $tht;
    public $leher;
    public $toraks;
    public $jantung;
    public $paru;
    public $abdomen;
    public $genitalia_anus;
    public $ekstremitas;
    public $kulit;
    public $rencana;

    public $kategori_asmed;
    public $is_merokok_pasif;
    public $kategori_denyut_nadi;
    public $kesadaran;
    public $keadaan_umum;

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

    public $terintubasi_lainnya;
    public $luka_bakar;
    public $berat_luka_bakar;
    public $allo_or_auto;

    //anak
    public $paritas;
    public $paritas_lainnya;
    public $abortus;
    public $abortus_lainnya;
    public $meninggal;
    public $meninggal_lainnya;
    public $partus_dokter;
    public $partus_bidan;
    public $partus;
    public $partus_lainnya;
    public $lama_kehamilan;
    public $komplikasi;
    public $komplikasi_lainnya;
    public $neotanus;
    public $neotanus_lainnya;
    public $maternal;
    public $maternal_lainnya;
    public $aa;

    public $berat_badan_anak;
    public $tinggi_badan_anak;
    public $kelainan_anak;
    public $asi;
    public $asi_addon;
    public $susu_formula;
    public $susu_formula_addon;
    public $makanan_padat;
    public $makanan_padat_addon;
    public $makanan_tambahan;
    public $makanan_tambahan_addon;
    public $tengkurap;
    public $tengkurap_addon;
    public $duduk;
    public $duduk_addon;
    public $merangkak;
    public $merangkak_addon;
    public $berdiri;
    public $berdiri_addon;
    public $berjalan;
    public $berjalan_addon;

    public $anatomi_tubuh;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'dokter_id', 'keluhan_utama', 'tgl_asesmenmedis'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 'lama_sakit', 'jumlah_rokok', 'td_systolic', 'td_diastolic', 'imt', 'detak_nadi', 'pernapasan', 'suhu_tubuh'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 'lama_sakit', 'jumlah_rokok', 'td_systolic', 'td_diastolic', 'imt', 'detak_nadi', 'pernapasan', 'suhu_tubuh','tinggi_badan', 'skala','kategori_asmed', 'kesadaran', 'keadaan_umum'], 'safe'],
            [['keluhan_utama', 'keluhan_tambahan', 'r_penyakitsekarang', 'r_penyakitdahulu', 'r_imunisasi', 'r_peskk', 'obat_diberikan', 'r_makanan', 'r_kelahiran', 'r_alergiobat', 'keterangan',
             'inspeksi_kepala', 'palpasi_kepala', 'neurologi_kepala', 'jvp', 'kgb', 'inspeksi_toraks', 
             'palpasi_toraks', 'perkusi_toraks', 'auskultasi_toraks', 'inspeksi_punggung', 'palpasi_punggung',
             'perkusi_punggung', 'auskultasi_punggung', 'inspeksi_abdomen', 'palpasi_abdomen', 'perkusi_abdomen',
            'auskultasi_abdomen', 'hepar', 'lien', 'inspeksi_ekstrim', 'palpasi_ekstrim', 'neurologi_ekstrim', 
            'kepala_lainnya', 'mata_lainnya', 'tht_lainnya', 'leher_lainnya', 'mulut_lainnya', 'thoraks_lainnya', 'perkusi_lainnya', 
            'pernapasan_lainnya', 'bunyi_jantung_lainnya', 'kelaianan_lainnya', 'benjolan_lainnya', 'nyeri_tekan_lainnya',
            'hernia_lainnya', 'bising_usus_lainnya', 'distensi_lainnya', 'tulang_belakang_lainnya', 'sistem_saraf_lainnya',
            'genetalia_lainnya', 'edema_lainnya', 'crt_lainnya', 'pemeriksaan_fisik_lainnya',
            'anus_genitalia', 'catatan_rad', 'catatan_lab', 'masalah', 'diagnosa_id', 'berat_luka_bakar', 'terintubasi_lainnya',
            'paritas_lainnya', 'abortus_lainnya', 'meninggal_lainnya', 'komplikasi_lainnya', 'neotanus_lainnya', 'maternal_lainnya',
            'kelainan_anak', 'asi_addon', 'susu_formula_addon', 'makanan_padat_addon', 'makanan_tambahan_addon', 'tengkurap_addon',
            'duduk_addon', 'berdiri_addon', 'berjalan_addon',
            ], 'string'],
            [[
                'tgl_asesmenmedis','discharge_plan','care_plan','asesmenmedis_id','rencana','kepala','mulut','mata',
                'tht','leher','toraks','jantung','paru',
                'abdomen','genitalia_anus','ekstremitas','kulit', 'pergerakan', 'perkusi', 'nafas', 'rochi', 'wheezing',
                'irama','bunyi_jantung','kelainan','benjolan', 'nyeri_tekan', 'hernia', 'bising_usus', 'distensi', 'tulang_belakang',
                'sistem_saraf', 'genetalia', 'edema', 'crt',
                'paritas', 'abortus', 'meninggal', 'partus_dokter', 'partus_bidan', 'partus', 'partus_lainnya', 'lama_kehamilan', 'komplikasi',
                'neotanus', 'maternal'
            ], 'safe'],
            [['is_merokok', 'is_terintubasi', 'is_kakududuk', 'is_merokok_pasif'], 'boolean'], 
            [[
                'tinggi_badan', 'berat_badan', 'bb_ideal', 'skala', 'luka_bakar', 'gcs_eye', 'gcs_verbal', 'gcs_motorik',
                'is_kapitis', 'jumlah_gcs', 'berat_badan_anak', 'tinggi_badan_anak', 'asi', 'susu_formula' , 'makanan_padat', 'makanan_tambahan',
                'tengkurap', 'duduk', 'merangkak', 'berdiri', 'berjalan'
            ], 'safe'],
            [['tekanan_darah', 'ket_imt', 'denyut_jantung', 'hasil_gcs', 'kontak', 'metod_asmennyeri', 'skala', 'lokasi_nyeri', 'kategori_denyut_nadi'], 'string', 'max' => 100],
            [['sumber_hubungan', 'hasil_td'], 'string', 'max' => 255],

            [['sumber_hubungan'], 'required', 'when' => function ($model) {
                return $model->sumber_info_lainnya == 1;
            }],

            [['benjolan_lainnya'], 'required', 'when' => function ($model) {
                return $model->benjolan == 0 && $model->benjolan != '';
            }],
            [['bunyi_jantung_lainnya'], 'required', 'when' => function ($model) {
                return $model->bunyi_jantung == 0 && $model->bunyi_jantung != '';
            }],

            [['sumber_info', 'sumber_info_lainnya'], 'validateCb']
            
        ];
    }

    public function validateCb($attribute, $params)
    {
        if ($this->sumber_info == 0 && $this->sumber_info_lainnya == 0) {
            $this->addError($attribute, Yii::t('fe', 'Sumber Informasi Tidak Boleh Kosong'));
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'dokter_id' => Yii::t('fe', 'Dokter'),
            'keluhan_utama' => Yii::t('fe','Keluhan utama'),
            'keluhan_tambahan' => Yii::t('fe' ,'Keluhan tambahan'),
            'r_penyakitsekarang' => Yii::t('fe','Riwayat penyakit sekarang'),
            'lama_sakit' => Yii::t('fe','Lama sakit'),
            'r_penyakitdahulu' => Yii::t('fe','Riwayat Penyakit dahulu'),
            'r_penyakitkeluarga' => Yii::t('fe','Riwayat Penyakit keluarga'),
            'r_imunisasi' => Yii::t('fe','Riwayat Imunisasi'),
            'r_peskk' => Yii::t('fe','Riwayat pekerjaan, sosial, ekonomi, kejiwaan, dan kebiasaan'),
            'tgl_asesmenmedis' => Yii::t('fe','Tanggal Asesmen medis'),
            'sumber_info' => Yii::t('fe','Sumber informasi'),
            'sumber_informasi' => Yii::t('fe','Sumber informasi'),
            'sumber_hubungan' => Yii::t('fe','Sumber Hubungan'),
            'is_merokok' => Yii::t('fe','Status Merokok'),
            'jumlah_rokok' => Yii::t('fe','Jumlah batang rokok'),
            'obat_diberikan' => Yii::t('fe','Obat yang sudah diberikan'),
            'r_makanan' => Yii::t('fe','Riwayat Makanan'),
            'r_kelahiran' => Yii::t('fe','Riwayat Kelahiran'),
            'r_alergiobat' => Yii::t('fe','Riwayat Alergi obat'),
            'keterangan' => Yii::t('fe','Keterangan Anamnesa'),
            'td_systolic' => Yii::t('fe','Td Systolic'),
            'td_diastolic' => Yii::t('fe','Td Diastolic'),
            'tekanan_darah' => Yii::t('fe','Tekanan Darah'),
            'hasil_td' => Yii::t('fe','Hasil Td'),
            'tinggi_badan' => Yii::t('fe','Tinggi Badan'),
            'berat_badan' => Yii::t('fe','Berat Badan'),
            'bb_ideal' => Yii::t('fe','BB Ideal'),
            'imt' => Yii::t('fe','IMT'),
            'ket_imt' => Yii::t('fe','Keterangan IMT'),
            'detak_nadi' => Yii::t('fe','Denyut Nadi'),
            'pernapasan' => Yii::t('fe','Pernapasan'),
            'denyut_jantung' => Yii::t('fe','Denyut Jantung'),
            'suhu_tubuh' => Yii::t('fe','Suhu Tubuh'),
            'gcs_eye' => Yii::t('fe','GCS Eye'),
            'gcs_verbal' => Yii::t('fe','GCS Verbal'),
            'gcs_motorik' => Yii::t('fe','GCS Motorik'),
            'jumlah_gcs' => Yii::t('fe','Jumlah GCS'),
            'hasil_gcs' => Yii::t('fe','Hasil GCS'),
            'kontak' => Yii::t('fe','Kontak'),
            'metod_asmennyeri' => Yii::t('fe','Metode Asesmen Nyeri'),
            'is_terintubasi' => Yii::t('fe','Terintubasi'),
            'skala' => Yii::t('fe','Skala'),
            'lokasi_nyeri' => Yii::t('fe','Lokasi Nyeri'),
            'inspeksi_kepala' => Yii::t('fe','Inspeksi Kepala'),
            'palpasi_kepala' => Yii::t('fe','Palpasi Kepala'),
            'neurologi_kepala' => Yii::t('fe','Neurologi Kepala'),
            'is_kakududuk' => Yii::t('fe','Kaku duduk'),
            'jvp' => Yii::t('fe','Jvp'),
            'kgb' => Yii::t('fe','Kgb'),
            'inspeksi_toraks' => Yii::t('fe','Inspeksi Toraks'),
            'palpasi_toraks' => Yii::t('fe','Palpasi Toraks'),
            'perkusi_toraks' => Yii::t('fe','Perkusi Toraks'),
            'auskultasi_toraks' => Yii::t('fe','Auskultasi Toraks'),
            'inspeksi_punggung' => Yii::t('fe','Inspeksi Punggung'),
            'palpasi_punggung' => Yii::t('fe','Palpasi Punggung'),
            'perkusi_punggung' => Yii::t('fe','Perkusi Punggung'),
            'auskultasi_punggung' => Yii::t('fe','Auskultasi Punggung'),
            'inspeksi_abdomen' => Yii::t('fe','Inspeksi Abdomen'),
            'palpasi_abdomen' => Yii::t('fe','Palpasi Abdomen'),
            'perkusi_abdomen' => Yii::t('fe','Perkusi Abdomen'),
            'auskultasi_abdomen' => 'Auskultasi Abdomen',
            'hepar' => Yii::t('fe','Hepar'),
            'lien' => Yii::t('fe','Lien'),
            'inspeksi_ekstrim' => Yii::t('fe','Inspeksi Ekstrim'),
            'palpasi_ekstrim' => Yii::t('fe','Palpasi Ekstrim'),
            'neurologi_ekstrim' => Yii::t('fe','Neurologi Ekstrim'),
            'anus_genitalia' => Yii::t('fe','Anus Genitalia (hanya bila diperlukan)'),
            'catatan_rad' => Yii::t('fe','Catatan Rad'),
            'catatan_lab' => Yii::t('fe','Catatan Lab'),
            'diagnosa_id' => Yii::t('fe','Diagnosa'),
            'masalah' => Yii::t('fe','Masalah'),
            'is_kapitis' => Yii::t('fe','Kapitis'),
            'kepala' => Yii::t('fe','Kepala'),
            'mulut' => Yii::t('fe','Mulut'),
            'mata' => Yii::t('fe','Mata'),
            'tht' => Yii::t('fe','THT'),
            'leher' => Yii::t('fe','Leher'),
            'toraks' => Yii::t('fe','Thoraks'),
            'jantung' => Yii::t('fe','Jantung'),
            'paru' => Yii::t('fe','Paru'),
            'abdomen' => Yii::t('fe','Abdomen'),
            'genitalia_anus' => Yii::t('fe','Genitalia & Anus'),
            'ekstremitas' => Yii::t('fe','Ekstremitas'),
            'kulit' => Yii::t('fe','Kulit'),
            'rencana' => Yii::t('fe','Rencana'),
            'kategori_asmed' => Yii::t('fe','Kategori Asesmen Awal Medis'),
            'jumlah_rokok' => Yii::t('fe','Jumlah Batang Rokok'),
            'kategori_denyut_nadi' => Yii::t('fe','Kategori Denyut Nadi'),
            'kesadaran' => Yii::t('fe','Kesadaran'),
            'keadaan_umum' => Yii::t('fe','Keadaan Umum'),
            'pergerakan' => Yii::t('fe','Pergerakan'),
            'perkusi' => Yii::t('fe','Perkusi'),
            'nafas' => Yii::t('fe','Nafas'),
            'rochi' => Yii::t('fe','Rochi'),
            'wheezing' => Yii::t('fe','Wheezing'),
            'irama' => Yii::t('fe','Irama'),
            'bunyi_jantung' => Yii::t('fe','Bunyi Jantung'),
            'kelainan' => Yii::t('fe','Kelainan'),
            'benjolan' => Yii::t('fe','Benjolan'),
            'nyeri_tekan' => Yii::t('fe','Nyeri Tekan'),
            'hernia' => Yii::t('fe','Hernia'),
            'bising_usus' => Yii::t('fe','Bising Usus'),
            'distensi' => Yii::t('fe','distensi'),
            'tulang_belakang' => Yii::t('fe','Tulang Belakang'),
            'luka_bakar' => Yii::t('fe','Luka Bakar'),
            'is_merokok_pasif' => Yii::t('fe','Status Merokok'),
            'lama_kehamilan' => Yii::t('fe','Lama Kehamilan'),
            'komplikasi' => Yii::t('fe','Komplikasi'),
            'neotanus' => Yii::t('fe','Masalah Neotanus'),
            'maternal' => Yii::t('fe','Masalah Maternal'),
            'aa' => 'x',
            'benjolan_lainnya' => Yii::t('fe','Field Benjolan'),
            'bunyi_jantung_lainnya' => Yii::t('fe','Field Bunyi Jantung'),
            'pemeriksaan_fisik_lainnya' => Yii::t('fe', 'Lain-lain'),
            'allo_or_auto' => 'allo_or_auto',
        ];
    }
}
