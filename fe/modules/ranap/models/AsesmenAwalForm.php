<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "asesmenawal_t".
 *
 * @property int $asesmenawal_id

 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id

 * @property string $waktu_tiba

 * @property string $asal_masuk
 instalasi_id JOIN instalasi_m --> instalasi_nama
 * @property string $tgl_asesmen

 * @property string $asesmen_diambildari
 lookupkeperawatan_m.lookup_type='asmen_dari' -->lookup_value
 * @property string $diambildari_nama
 jika asmen_dari=orang lain
 * @property string $diambildari_hub
 jika asmen_dari=orang lain
 * @property string $masuk_dengan
 lookupkeperawatan_m.lookup_type='asmen_masukdengan' --> lookup_value
 * @property string $masuk_denganlain
 jika asmen_masukdengan=lain-lain
 * @property bool $obat_darirumah
 * @property string $obatan_rumah jika obat_darirumah=TRUE
 * @property bool $hasil_pemeriksaan
 * @property string $hasil_rad
 * @property string $hasil_lab
 * @property string $hasil_lainnya
 * @property string $keluhan_utama
 * @property int $diagnosa_masuk
 * @property int $r_kehamilan_g
 * @property int $r_kehamilan_p
 * @property int $r_kehamilan_a
 * @property string $hpht
 * @property bool $haid_teratur
 * @property string $ketergantungan lookupkeperawatan_m.lookup_type='asmen_ketergantungan' --> lookup_value
 * @property string $r_penyakit_kel lookupkeperawatan_m.lookup_type='asmen_penyakit_kel' --> lookup_value
 * @property string $penyakit_kel_lain jika asmen_penyakit_kel=lainnya
 * @property string $r_kes_sekarang
 * @property bool $pernah_dirawat
 * @property string $tgl_dirawat
 * @property string $alasan_dirawat
 * @property bool $pernah_tindakan
 * @property string $tgl_tindakan
 * @property string $alasan_tindakan
 * @property int $jeniskegiatantindakan_id jeniskegiatan_m
 * @property bool $r_alergi
 * @property string $nama_alergi
 * @property bool $transfusi
 * @property string $reaksi
 * @property string $asmen_nyeri data dalam bentuk JSON
 * @property string $asmen_resikojatuh data dalam bentuk JSON
 * @property string $asmen_tandavital data dalam bentuk JSON
 * @property string $asmen_kes_kuantitatif data dalam bentuk JSON
 * @property string $asmen_kes_kualitatif data dalam bentuk JSON
 * @property string $asmen_rambutkepala data dalam bentuk JSON
 * @property string $asmen_mata data dalam bentuk JSON
 * @property string $asmen_hidung data dalam bentuk JSON
 * @property string $asmen_telinga data dalam bentuk JSON
 * @property string $asmen_mulut data dalam bentuk JSON
 * @property string $asmen_leher data dalam bentuk JSON
 * @property string $asmen_dada data dalam bentuk JSON
 * @property string $asmen_payudara data dalam bentuk JSON
 * @property string $asmen_jantung data dalam bentuk JSON
 * @property string $asmen_tulangbelakang data dalam bentuk JSON
 * @property string $asmen_abdomen data dalam bentuk JSON
 * @property string $asmen_genitalia data dalam bentuk JSON
 * @property string $asmen_ekstremitas data dalam bentuk JSON
 * @property string $asmen_kebutuhandasar data dalam bentuk JSON
 * @property string $asmen_skriningfungsional data dalam bentuk JSON
 * @property string $asmen_skrininggizi data dalam bentuk JSON
 * @property string $asmen_kebutuhanpend data dalam bentuk JSON
 * @property string $asmen_masalahkes data dalam bentuk JSON
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
class AsesmenAwalForm extends \yii\base\Model
{
    public $waktulain;
    public $waktu_tiba;
    public $asal_masuk;
    public $tgl_asesmen;
    public $asesmen_diambildari;
    public $masuk_dengan;
    public $obat_darirumah;
    public $hasil_pemeriksaan;
    public $keluhan_utama;
    public $r_kes_sekarang;
    public $r_kehamilan_g;
    public $r_kehamilan_p;
    public $r_kehamilan_a;
    public $nyeri_lain_lain;
    public $nyeri_lain_lain2;
    public $keluhan_nyeri;
    public $hasil_lab_opsional;
    public $hasil_rad_opsional;
    public $hasil_lainnya_opsional;
    public $asmen_nyeri;
    public $diambildari_nama;
    public $diambildari_hub;
    public $masuk_denganlain;
    public $diagnosa_masuk;
    public $hpht;
    public $haid_teratur;
    public $pernah_dirawat;
    public $ketergantungan;
    public $r_penyakit_kel;
    public $penyakit_kel_lain;
    public $tgl_dirawat;
    public $alasan_dirawat;
    public $pernah_tindakan;
    public $tgl_tindakan;
    public $jeniskegiatantindakan_id;
    public $r_alergi;
    public $nama_alergi;
    public $transfusi;
    public $reaksi;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $created_by;
    public $modified_count;
    public $last_modified_by;
    public $deleted_by;
    public $is_deleted;
    public $is_active;
    public $alasan_tindakan;
    public $hasil_rad;
    public $hasil_lab;
    public $hasil_lainnya;
    public $obatan_rumah;
    public $asmen_riwayat;
    public $asmen_periksafisik;
    public $asmen_kebdasar;
    public $asmen_keb_dasar;
    public $asmen_sk_fungsional;
    public $asmen_sk_gizi;
    public $asmen_masalahkes;
    public $asmen_keb_pendidikan;
    public $additional_data;
    // public $hal-hal_yang_membantu_cepat_tidur;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'diagnosa_masuk', 'r_kehamilan_g', 'r_kehamilan_p', 'r_kehamilan_a', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'r_kehamilan_g', 'r_kehamilan_p', 'r_kehamilan_a', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                 'pendaftaran_id','pasienadmisi_id',
                 'waktu_tiba','asal_masuk','tgl_asesmen','asesmen_diambildari',
                 'diambildari_nama','diambildari_hub','masuk_dengan','masuk_denganlain',
                 'obat_darirumah','obatan_rumah','hasil_pemeriksaan','hasil_rad',
                 'hasil_lab','hasil_lainnya','keluhan_utama','diagnosa_masuk','r_kehamilan_g',
                 'r_kehamilan_p','r_kehamilan_a','hpht','haid_teratur',
                 'ketergantungan','r_penyakit_kel','penyakit_kel_lain','r_kes_sekarang',
                 'pernah_dirawat','tgl_dirawat','alasan_dirawat',
                 'pernah_tindakan','tgl_tindakan','alasan_tindakan',
                 'jeniskegiatantindakan_id ','r_alergi','nama_alergi','transfusi',
                 'reaksi', 'asmen_riwayat','asmen_periksafisik', 'asmen_keb_dasar',
                 'asmen_sk_fungsional','asmen_sk_gizi','asmen_keb_pendidikan',
                 'asmen_masalahkes','additional_data','created_date','created_by',
                 'modified_count','last_modified_date','last_modified_by',
                 'is_deleted','is_active','deleted_date','deleted_by',
            ], 'safe'],
            [['obat_darirumah', 'hasil_pemeriksaan', 'haid_teratur', 'pernah_dirawat', 'pernah_tindakan', 'r_alergi', 'transfusi', 'is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_rekam_medik' => \Yii::t('fe', 'No. Rekam Medik'),
            'asesmenawal_id' => 'Asesmenawal ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'waktu_tiba' => \Yii::t('fe', 'Waktu Pasien tiba di ruangan'),
            'asal_masuk' => 'Asal Masuk',
            'tgl_asesmen' => 'Tanggal Asesmen',
            'asesmen_diambildari' => 'Asesmen diambil dari',
            'diambildari_nama' => 'Diambildari Nama',
            'diambildari_hub' => 'Diambildari Hub',
            'masuk_dengan' => 'Masuk dengan',
            'masuk_denganlain' => 'Masuk Denganlain',
            'obat_darirumah' => 'Obat - obatan dari rumah',
            'obatan_rumah' => 'Obatan Rumah',
            'hasil_pemeriksaan' => 'Hasil pemeriksaan yang dibawa keluarga',
            'hasil_rad' => 'Radiologi',
            'hasil_lab' => 'Laboratorium',
            'hasil_lainnya' => 'Diagnostik Lain',
            'keluhan_utama' => 'Keluhan Utama',
            'diagnosa_masuk' => 'Diagnosa Masuk',
            'r_kehamilan_g' => 'R Kehamilan G',
            'r_kehamilan_p' => 'R Kehamilan P',
            'r_kehamilan_a' => 'R Kehamilan A',
            'hpht' => 'Hpht',
            'haid_teratur' => 'Haid Teratur',
            'ketergantungan' => 'Ketergantungan',
            'r_penyakit_kel' => 'Riwayat Penyakit Keluarga',
            'penyakit_kel_lain' => 'Penyakit Keluarga Lain',
            'r_kes_sekarang' => 'Riwayat Kesehatan Sekarang',
            'pernah_dirawat' => 'Pernah Dirawat',
            'tgl_dirawat' => 'Tanggal Dirawat',
            'alasan_dirawat' => 'Alasan',
            'pernah_tindakan' => 'Operasi/Tindakan',
            'tgl_tindakan' => 'Tanggal Tindakan',
            'alasan_tindakan' => 'Alasan Tindakan',
            'jeniskegiatantindakan_id' => 'Jenis',
            'r_alergi' => 'Riwayat Alergi',
            'nama_alergi' => 'Nama Alergi',
            'transfusi' => 'Transfusi Darah',
            'reaksi' => 'Reaksi',
            'asmen_nyeri' => 'Asmen Nyeri',
            'asmen_resikojatuh' => 'Asmen Resikojatuh',
            'asmen_tandavital' => 'Asmen Tandavital',
            'asmen_kes_kuantitatif' => 'Asmen Kes Kuantitatif',
            'asmen_kes_kualitatif' => 'Asmen Kes Kualitatif',
            'asmen_rambutkepala' => 'Asmen Rambutkepala',
            'asmen_mata' => 'Asmen Mata',
            'asmen_hidung' => 'Asmen Hidung',
            'asmen_telinga' => 'Asmen Telinga',
            'asmen_mulut' => 'Asmen Mulut',
            'asmen_leher' => 'Asmen Leher',
            'asmen_dada' => 'Asmen Dada',
            'asmen_payudara' => 'Asmen Payudara',
            'asmen_jantung' => 'Asmen Jantung',
            'asmen_tulangbelakang' => 'Asmen Tulangbelakang',
            'asmen_abdomen' => 'Asmen Abdomen',
            'asmen_genitalia' => 'Asmen Genitalia',
            'asmen_ekstremitas' => 'Asmen Ekstremitas',
            'asmen_kebutuhandasar' => 'Asmen Kebutuhandasar',
            'asmen_skriningfungsional' => 'Asmen Skriningfungsional',
            'asmen_skrininggizi' => 'Asmen Skrininggizi',
            'asmen_kebutuhanpend' => 'Asmen Kebutuhanpend',
            'asmen_masalahkes' => 'Asmen Masalahkes',
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

    public function formatted($attribute) {
        return $this->format($this->$attribute);
    }
}
