<?php


namespace Doco\models;

use Yii;

/**
 * This is the model class for table "asesmenmedis_t".
 *
 * @property int $asesmenmedis_id

 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $dokter_id
 * @property string $keluhan_utama

 * @property string $keluhan_tambahan

 * @property string $r_penyakitsekarang

 * @property int $lama_sakit

 * @property string $r_penyakitdahulu

 * @property string $r_penyakitkeluarga

 * @property string $r_imunisasi

 * @property string $r_peskk

 * @property string $tgl_asesmenmedis

 * @property string $sumber_info

 * @property string $sumber_info_lainnya

 * @property string $sumber_hubungan

 * @property bool $is_merokok

 * @property string $jumlah_rokok

 * @property string $obat_diberikan

 * @property string $r_makanan

 * @property string $r_kelahiran

 * @property string $r_alergiobat

 * @property string $keterangan

 * @property int $td_systolic

 * @property int $td_diastolic

 * @property string $tekanan_darah

 * @property string $hasil_td

 * @property double $tinggi_badan

 * @property double $berat_badan

 * @property double $bb_ideal

 * @property int $imt

 * @property string $ket_imt

 * @property int $detak_nadi

 * @property int $pernapasan

 * @property string $denyut_jantung
 * @property int $suhu_tubuh

 * @property int $gcs_eye

 * @property int $gcs_verbal

 * @property int $gcs_motorik

 * @property string $hasil_gcs

 * @property string $kontak
 * @property string $metod_asmennyeri
 * @property bool $is_terintubasi

 * @property string $skala

 * @property string $lokasi_nyeri

 * @property string $inspeksi_kepala

 * @property string $palpasi_kepala

 * @property string $neurologi_kepala

 * @property bool $is_kakududuk

 * @property string $jvp

 * @property string $kgb
 * @property string $inspeksi_toraks

 * @property string $palpasi_toraks

 * @property string $perkusi_toraks

 * @property string $auskultasi_toraks

 * @property string $inspeksi_punggung

 * @property string $palpasi_punggung

 * @property string $perkusi_punggung

 * @property string $auskultasi_punggung

 * @property string $inspeksi_abdomen

 * @property string $palpasi_abdomen

 * @property string $perkusi_abdomen

 * @property string $auskultasi_abdomen

 * @property string $hepar

 * @property string $lien

 * @property string $inspeksi_ekstrim

 * @property string $palpasi_ekstrim

 * @property string $neurologi_ekstrim

 * @property string $anus_genitalia

 * @property string $catatan_rad

 * @property string $catatan_lab

 * @property int $diagnosa_id

 * @property string $masalah

 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $kepala 
 * @property string $mulut 
 * @property string $mata 
 * @property string $tht 
 * @property string $leher 
 * @property string $toraks 
 * @property string $jantung 
 * @property string $paru 
 * @property string $abdomen 
 * @property string $genitalia_anus 
 * @property string $ekstremitas 
 * @property string $kulit 
 * @property string $rencana 
 * 
 * 
 * @property int $kategori_asmed
 * @property bool $is_merokok_pasif
 * @property string $kategori_denyut_nadi  
 * @property int $keadaan_umum 
 * @property int $kesadaran_umum 
 * 
 * @property int $pergerakan
 * @property int $perkusi
 * @property int $nafas
 * @property int $rochi
 * @property int $wheezing
 * @property int $irama
 * @property int $bunyi_jantung
 * @property int $kelainan
 * @property int $benjolan
 * @property int $nyeri_tekan
 * @property int $hernia
 * @property int $bising_usus
 * @property int $distensi
 * @property int $tulang_belakang
 * @property int $sistem_saraf
 * @property int $genetalia
 * @property int $edema
 * @property int $crt

 * 
 * @property string $kepala_lainnya 
 * @property string $mata_lainnya 
 * @property string $tht_lainnya 
 * @property string $leher_lainnya
 * @property string $mulut_lainnya
 * @property string $thoraks_lainnya
 * @property string $perkusi_lainnya
 * @property string $pernapasan_lainnya
 * @property string $bunyi_jantung_lainnya
 * @property string $kelaianan_lainnya
 * @property string $benjolan_lainnya
 * @property string $nyeri_tekan_lainnya
 * @property string $hernia_lainnya
 * @property string $bising_usus_lainnya
 * @property string $distensi_lainnya
 * @property string $tulang_belakang_lainnya
 * @property string $terintubasi_lainnya
 * @property string $sistem_saraf_lainnya
 * @property string $genetalia_lainnya
 * @property string $edema_lainnya
 * @property string $crt_lainnya
 * @property string $pemeriksaan_fisik_lainnya

 * @property double $luka_bakar
 * 
 * 
 * @property int $paritas
 * @property string $paritas_lainnya
 * @property int $abortus
 * @property string $abortus_lainnya
 * @property int $meninggal
 * @property string $meninggal_lainnya
 * @property int $partus_dokter
 * @property int $partus_bidan
 * @property int $partus
 * @property string $partus_lainnya
 * @property string $lama_kehamilan
 * @property int $komplikasi
 * @property string $komplikasi_lainnya
 * @property int $neotanus
 * @property string $neotanus_lainnya
 * @property int $maternal
 * @property string $maternal_lainnya
 * @property string $r_tumbuh_kembang
 * 
 */
 

class AsesmenMedis extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asesmenmedis_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'dokter_id', 'keluhan_utama', 'tgl_asesmenmedis'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 'lama_sakit', 'jumlah_rokok', 'td_systolic', 'td_diastolic', 'imt', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'gcs_eye', 'gcs_verbal', 'r_penyakitkeluarga', 'gcs_motorik', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 'lama_sakit', 'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'gcs_eye', 
              'gcs_verbal', 'gcs_motorik', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','kategori_asmed', 'keadaan_umum', 'kesadaran_umum',
              'pergerakan', 'perkusi' , 'nafas', 'rochi', 'wheezing', 'irama', 'bunyi_jantung', 'kelainan', 'benjolan', 'nyeri_tekan',
              'paritas', 'abortus', 'meninggal', 'partus_dokter', 'partus_bidan', 'partus', 'komplikasi', 'neotanus', 'maternal',
            ], 'integer'],
            [['keluhan_utama', 'keluhan_tambahan', 'r_penyakitsekarang', 'r_penyakitdahulu', 'r_imunisasi', 'r_peskk', 'obat_diberikan', 'r_makanan', 
            'r_kelahiran', 'r_alergiobat', 'keterangan', 'inspeksi_kepala', 'palpasi_kepala', 'neurologi_kepala', 'jvp', 'kgb', 'inspeksi_toraks',
             'palpasi_toraks', 'perkusi_toraks', 'auskultasi_toraks', 'inspeksi_punggung', 'palpasi_punggung', 'perkusi_punggung', 'auskultasi_punggung', 
             'inspeksi_abdomen', 'palpasi_abdomen', 'perkusi_abdomen', 'auskultasi_abdomen', 'hepar', 'lien', 'inspeksi_ekstrim', 'palpasi_ekstrim',
             'kepala_lainnya', 'mata_lainnya', 'tht_lainnya', 'leher_lainnya', 'mulut_lainnya', 'thoraks_lainnya', 'perkusi_lainnya', 
             'pernapasan_lainnya', 'bunyi_jantung_lainnya', 'kelaianan_lainnya', 'benjolan_lainnya', 'nyeri_tekan_lainnya',
             'hernia_lainnya', 'bising_usus_lainnya', 'distensi_lainnya', 'tulang_belakang_lainnya', 'terintubasi_lainnya',
             'sistem_saraf_lainnya','genetalia_lainnya', 'edema_lainnya', 'crt_lainnya', 'pemeriksaan_fisik_lainnya',
             'paritas_lainnya', 'abortus_lainnya', 'meninggal_lainnya', 'partus_lainnya', 'lama_kehamilan', 'komplikasi_lainnya', 
             'neotanus_lainnya', 'maternal_lainnya', 'r_tumbuh_kembang',
              'neurologi_ekstrim', 'anus_genitalia', 'catatan_rad', 'catatan_lab', 'masalah', 'additional_data'], 'string'],
            [['tgl_asesmenmedis','discharge_plan', 'care_plan', 'created_date', 'last_modified_date', 'deleted_date',
             'is_kapitis', 'sumber_info_lainnya','rencana','kepala','mulut','mata','tht','leher','toraks','jantung',
             'paru','abdomen','genitalia_anus','ekstremitas','kulit'], 'safe'],
            [['is_merokok', 'is_terintubasi', 'is_kakududuk', 'is_deleted', 'is_active', 'is_kapitis','is_merokok_pasif'
            , 'hernia', 'bising_usus', 'distensi', 'tulang_belakang','sistem_saraf', 'genetalia', 'edema', 'crt',], 'boolean'],
            [['tinggi_badan', 'berat_badan', 'bb_ideal', 'imt', 'luka_bakar'], 'number'],
            [['sumber_info', 'tekanan_darah', 'ket_imt', 'denyut_jantung', 'hasil_gcs', 'kontak', 'metod_asmennyeri', 'skala', 'lokasi_nyeri', 'kategori_denyut_nadi'], 'string', 'max' => 100],
            [['sumber_hubungan', 'hasil_td'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asesmenmedis_id' => 'Asesmenmedis ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'dokter_id' => 'Dokter ID',
            'keluhan_utama' => 'Keluhan Utama',
            'keluhan_tambahan' => 'Keluhan Tambahan',
            'r_penyakitsekarang' => 'R Penyakitsekarang',
            'lama_sakit' => 'Lama Sakit',
            'r_penyakitdahulu' => 'R Penyakitdahulu',
            'r_penyakitkeluarga' => 'R Penyakitkeluarga',
            'r_imunisasi' => 'R Imunisasi',
            'r_peskk' => 'R Peskk',
            'tgl_asesmenmedis' => 'Tgl Asesmenmedis',
            'sumber_info' => 'Sumber Info',
            'sumber_hubungan' => 'Sumber Hubungan',
            'is_merokok' => 'Is Merokok',
            'jumlah_rokok' => 'Jml Rokok',
            'obat_diberikan' => 'Obat Diberikan',
            'r_makanan' => 'R Makanan',
            'r_kelahiran' => 'R Kelahiran',
            'r_alergiobat' => 'R Alergiobat',
            'keterangan' => 'Keterangan',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Td Diastolic',
            'tekanan_darah' => 'Tekanan Darah',
            'hasil_td' => 'Hasil Td',
            'tinggi_badan' => 'Tinggi Badan',
            'berat_badan' => 'Berat Badan',
            'bb_ideal' => 'Bb Ideal',
            'imt' => 'Imt',
            'ket_imt' => 'Ket Imt',
            'detak_nadi' => 'Detak Nadi',
            'pernapasan' => 'Pernapasan',
            'denyut_jantung' => 'Denyut Jantung',
            'suhu_tubuh' => 'Suhu Tubuh',
            'gcs_eye' => 'Gcs Eye',
            'gcs_verbal' => 'Gcs Verbal',
            'gcs_motorik' => 'Gcs Motorik',
            'hasil_gcs' => 'Hasil Gcs',
            'kontak' => 'Kontak',
            'metod_asmennyeri' => 'Metod Asmennyeri',
            'is_terintubasi' => 'Is Terintubasi',
            'skala' => 'Skala',
            'lokasi_nyeri' => 'Lokasi Nyeri',
            'inspeksi_kepala' => 'Inspeksi Kepala',
            'palpasi_kepala' => 'Palpasi Kepala',
            'neurologi_kepala' => 'Neurologi Kepala',
            'is_kakududuk' => 'Is Kakududuk',
            'jvp' => 'Jvp',
            'kgb' => 'Kgb',
            'inspeksi_toraks' => 'Inspeksi Toraks',
            'palpasi_toraks' => 'Palpasi Toraks',
            'perkusi_toraks' => 'Perkusi Toraks',
            'auskultasi_toraks' => 'Auskultasi Toraks',
            'inspeksi_punggung' => 'Inspeksi Punggung',
            'palpasi_punggung' => 'Palpasi Punggung',
            'perkusi_punggung' => 'Perkusi Punggung',
            'auskultasi_punggung' => 'Auskultasi Punggung',
            'inspeksi_abdomen' => 'Inspeksi Abdomen',
            'palpasi_abdomen' => 'Palpasi Abdomen',
            'perkusi_abdomen' => 'Perkusi Abdomen',
            'auskultasi_abdomen' => 'Auskultasi Abdomen',
            'hepar' => 'Hepar',
            'lien' => 'Lien',
            'inspeksi_ekstrim' => 'Inspeksi Ekstrim',
            'palpasi_ekstrim' => 'Palpasi Ekstrim',
            'neurologi_ekstrim' => 'Neurologi Ekstrim',
            'anus_genitalia' => 'Anus Genitalia',
            'catatan_rad' => 'Catatan Rad',
            'catatan_lab' => 'Catatan Lab',
            'diagnosa_id' => 'Diagnosa ID',
            'masalah' => 'Masalah',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'kepala' => 'Kepala',
            'mulut' => 'fe','Mulut',
            'mata' => 'Mata',
            'tht' => 'THT',
            'leher' => 'Leher',
            'toraks' => 'Toraks',
            'jantung' => 'Jantung',
            'paru' => 'Paru',
            'abdomen' => 'Abdomen',
            'genitalia_anus' => 'Genitalia & Anus',
            'ekstremitas' => 'Ekstremitas',
            'kulit' => 'Kulit',
            'rencana' => 'Rencana',
            'kategori_asmed' => 'Kategori Asmed',
            'status_merokok' => 'Status Merokok',
            
        ];
    }
}
