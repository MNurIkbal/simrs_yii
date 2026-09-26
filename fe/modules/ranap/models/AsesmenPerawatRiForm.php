<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "asesmenperawatrd_t".
 *
 * @property int $asesmenperawatrd_id
 * @property int $pendaftaran_id
 * @property int $ruangan_id
 * @property string $tgl_asesmen
 * @property int $perawat_id
 * @property string $perawat_nama
 * @property string $tgl_pendaftaran
 * @property string $tgl_datang
 * @property int $prioritas_triage 1=merah, 2=kuning, 3=hijau, 4=hitam
 * @property bool $is_trauma
 * @property int $pasien_datang lookup_m.lookup_type='pengantar'
 * @property int $jenis_asmenperawat lookupkeperawatan_m.lookup_type='jenis_asmen_perawat'
 * @property string $alasan_kunjungan
 * @property bool $is_alergi
 * @property bool $is_alergiobat
 * @property string $alergi_obat
 * @property bool $is_alergilainnya
 * @property string $alergi_lainnya
 * @property int $keadaan_umum lookupkeperawatan_m.lookup_type='keadaan_umum'
 * @property bool $is_nyeri
 * @property string $lokasi_nyeri
 * @property int $skala_nyeri
 * @property string $metode_nyeri
 * @property bool $is_resikojatuh update pendaftaran_t.label_gelang -->"alergi":"#8B0000"
 * @property string $keluhan
 * @property int $lama_sakit
 * @property string $r_penyakitdahulu
 * @property string $r_penyakitkeluarga
 * @property string $catatan_asesmen
 * @property int $gcseye_id
 * @property int $gcsverbal_id
 * @property int $gcsmotorik_id
 * @property string $hasil_gcs
 * @property bool $is_kapitis
 * @property double $td_systolic
 * @property double $td_diastolic
 * @property string $tekanan_darah
 * @property string $hasil_td
 * @property double $detak_nadi
 * @property double $pernapasan
 * @property double $suhu_tubuh
 * @property double $tinggi_badan
 * @property double $berat_badan
 * @property double $bb_ideal
 * @property double $imt
 * @property string $ket_imt
 * @property double $spo2
 * @property string $kelaianan_tubuh
 * @property string $detail_asesmen
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
 */
class AsesmenPerawatRiForm extends \yii\base\Model
{
    public $nilai_gcs;
    public $pendaftaran_id;
    public $ruangan_id;
    public $perawat_id;
    public $perawat_nama;
    public $prioritas_triage;
    public $pasien_datang;
    public $jenis_asmenperawat;
    public $keadaan_umum;
    public $skala_nyeri;
    public $gcseye_id;
    public $gcsverbal_id;
    public $gcsmotorik_id;
    public $td_systolic;
    public $td_diastolic;
    public $detak_nadi;
    public $pernapasan;
    public $suhu_tubuh;
    public $spo2;
    public $tgl_asesmen;
    public $tgl_datang;
    public $tgl_pendaftaran;
    public $alasan_kunjungan;
    public $alergi_obat;
    public $alergi_lainnya;
    public $metode_nyeri;
    public $keluhan;
    public $r_penyakitdahulu;
    public $r_penyakitkeluarga;
    public $catatan_asesmen;
    public $kelaianan_tubuh;
    public $is_alergi;
    public $detail_asesmen;
    public $is_alergiobat;
    public $is_trauma;
    public $is_alergilainnya;
    public $is_nyeri;
    public $is_kapitis;
    public $is_resikojatuh;
    public $tekanan_darah;
    public $hasil_td;
    public $ket_imt;
    public $lokasi_nyeri;
    public $lama_sakit;
    public $hasil_gcs;
    public $tinggi_badan;
    public $berat_badan;
    public $bb_ideal;
    public $imt;
    public $created_by;
    public $modified_count;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $asesmenperawatrd_id;
    public $additional_data;
    /**
     * {@inheritdoc}
     */

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_asesmen', 'perawat_nama', 'tgl_pendaftaran', 'tgl_datang', 'prioritas_triage', 'pasien_datang', 'jenis_asmenperawat', 'alasan_kunjungan', 'is_alergi', 'keadaan_umum' , 'is_nyeri', 'is_resikojatuh', 'keluhan', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'tinggi_badan', 'berat_badan', 'spo2', 'td_diastolic', 'td_systolic'], 'required'],
            [['alergi_obat'], 'required', 'on' => 'is_alergiobat_true'],
            [['alergi_lainnya'], 'required', 'on' => 'is_alergilainnya_true'],
            [['lokasi_nyeri', 'skala_nyeri', 'metode_nyeri'], 'required', 'on' => 'is_nyeri_true'],
            [['pendaftaran_id', 'ruangan_id', 'perawat_id', 'perawat_nama', 'prioritas_triage', 'pasien_datang', 'jenis_asmenperawat', 'keadaan_umum', 'skala_nyeri', 'lama_sakit', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'spo2', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['asesmenperawatrd_id', 'pendaftaran_id', 'ruangan_id', 'perawat_id', 'prioritas_triage', 'pasien_datang', 'jenis_asmenperawat', 'skala_nyeri', 'lama_sakit', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_asesmen', 'tgl_pendaftaran', 'tgl_datang', 'r_penyakitdahulu', 'keadaan_umum', 'metode_nyeri', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_trauma', 'is_alergi', 'is_alergiobat', 'is_alergilainnya', 'is_nyeri', 'is_resikojatuh', 'is_kapitis', 'is_deleted', 'is_active'], 'boolean'],
            [['alasan_kunjungan', 'alergi_obat', 'alergi_lainnya', 'keluhan', 'r_penyakitkeluarga', 'catatan_asesmen', 'kelaianan_tubuh', 'detail_asesmen', 'additional_data'], 'string'],
            [['tinggi_badan', 'berat_badan', 'bb_ideal', 'imt',  'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'spo2'], 'number'],
            [['lokasi_nyeri', 'hasil_gcs'], 'string', 'max' => 255],
            [['tekanan_darah', 'hasil_td', 'ket_imt'], 'string', 'max' => 100]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asesmenperawatrd_id' => 'Asesmenperawatrd ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_asesmen' => \Yii::t('fe', 'Tanggal Asesmen'),
            'perawat_id' => \Yii::t('fe', 'Perawat'),
            'tgl_pendaftaran' => \Yii::t('fe', 'Pasien Registrasi Pukul'),
            'tgl_datang' => \Yii::t('fe', 'Pasien Datang Pukul'),
            'prioritas_triage' => 'Prioritas Triage',
            'is_trauma' => 'Is Trauma',
            'pasien_datang' => 'Pasien Datang',
            'jenis_asmenperawat' => 'Jenis Asesmen Keperawatan',
            'alasan_kunjungan' => 'Alasan Kunjungan',
            'is_alergi' => \Yii::t('fe', 'Riwayat Alergi'),
            'is_alergiobat' => \Yii::t('fe', 'Obat'),
            'alergi_obat' => 'Alergi Obat',
            'is_alergilainnya' => \Yii::t('fe', 'Lainnya'),
            'alergi_lainnya' => 'Alergi Lainnya',
            'keadaan_umum' => 'Keadaan Umum',
            'is_nyeri' => \Yii::t('fe', 'Nyeri'),
            'lokasi_nyeri' => 'Lokasi Nyeri',
            'skala_nyeri' => 'Skala Nyeri',
            'metode_nyeri' => 'Metode Nyeri',
            'is_resikojatuh' => \Yii::t('fe', 'Risiko Jatuh'),
            'keluhan' => 'Keluhan',
            'lama_sakit' => 'Lama Sakit',
            'r_penyakitdahulu' => \Yii::t('fe', 'Riwayat Penyakit Terdahulu'),
            'r_penyakitkeluarga' => \Yii::t('fe', 'Riwayat Penyakit Keluarga'),
            'catatan_asesmen' => 'Catatan Asesmen',
            // 'gcseye_id' => \Yii::t('fe', 'Gcs eye'),
            // 'gcsverbal_id' => \Yii::t('fe', 'Gcsverbal ID',
            // 'gcsmotorik_id' => 'Gcsmotorik ID',
            // 'hasil_gcs' => 'Hasil Gcs',
            // 'is_kapitis' => 'Is Kapitis',
            
            'gcseye_id' => \Yii::t('fe', 'GCS Eye'),
            'gcsverbal_id' => \Yii::t('fe', 'GCS Verbal'),
            'gcsmotorik_id' => \Yii::t('fe', 'GCS Motorik'),
            'hasil_gcs' => \Yii::t('fe', 'Keterangan GCS'),
            'is_kapitis' => \Yii::t('fe', 'Kapitis'),
            
            'td_systolic' => 'Tekanan Darah Systolic',
            'td_diastolic' => 'Tekanan Darah Diastolic',
            'tekanan_darah' => 'Tekanan Darah',
            'hasil_td' => 'Hasil Tekanan Darah',
            'detak_nadi' => 'Detak Nadi',
            'pernapasan' => 'Pernapasan',
            'suhu_tubuh' => 'Suhu Tubuh',
            'tinggi_badan' => 'Tinggi Badan',
            'berat_badan' => 'Berat Badan',
            'bb_ideal' => 'Berat Badan Ideal',
            'imt' => 'Index Masa Tubuh',
            'ket_imt' => 'Keterangan Index Masa Tubuh',
            'spo2' => 'SPO2',
            'kelaianan_tubuh' => 'Kelainan Pada Bagian Tubuh',
            'detail_asesmen' => 'Detail Asesmen',
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
        ];
    }
}
