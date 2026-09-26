<?php

namespace app\modules\v1\models;

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
 * @property string $asmen_riwayat data dalam bentuk JSON
 * @property string $asmen_periksafisik data dalam bentuk JSON
 * @property string $asmen_keb_dasar data dalam bentuk JSON
 * @property string $asmen_sk_fungsional data dalam bentuk JSON
 * @property string $asmen_sk_gizi data dalam bentuk JSON
 * @property string $asmen_keb_pendidikan data dalam bentuk JSON
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
class AsesmenAwal extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asesmenawal_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'diagnosa_masuk', 'r_kehamilan_g', 'r_kehamilan_p', 'r_kehamilan_a', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'r_kehamilan_g', 'r_kehamilan_p', 'r_kehamilan_a', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                'asesmenawal_id', 'pendaftaran_id', 'pasienadmisi_id',
                'waktu_tiba', 'asal_masuk', 'tgl_asesmen', 'asesmen_diambildari',
                'diambildari_nama', 'diambildari_hub', 'masuk_dengan', 'masuk_denganlain',
                'obat_darirumah', 'obatan_rumah', 'hasil_pemeriksaan', 'hasil_rad',
                'hasil_lab', 'hasil_lainnya', 'keluhan_utama', 'diagnosa_masuk', 'r_kehamilan_g',
                'r_kehamilan_p', 'r_kehamilan_a', 'hpht', 'haid_teratur',
                'ketergantungan', 'r_penyakit_kel', 'penyakit_kel_lain', 'r_kes_sekarang',
                'pernah_dirawat', 'tgl_dirawat', 'alasan_dirawat',
                'pernah_tindakan', 'tgl_tindakan', 'alasan_tindakan',
                'jeniskegiatantindakan_id ', 'r_alergi', 'nama_alergi', 'transfusi',
                'reaksi', 'asmen_riwayat', 'asmen_periksafisik', 'asmen_keb_dasar',
                'asmen_sk_fungsional', 'asmen_sk_gizi', 'asmen_keb_pendidikan',
                'asmen_masalahkes', 'additional_data', 'created_date', 'created_by',
                'modified_count', 'last_modified_date', 'last_modified_by',
                'is_deleted', 'is_active', 'deleted_date', 'deleted_by', 'is_verifikasigizi', 'pegawaiverifikasigizi_id', 'tgl_verifikasi'
            ], 'safe'],
            [['is_verifikasigizi'], 'default', 'value' => false],
            [['obat_darirumah', 'hasil_pemeriksaan', 'haid_teratur', 'pernah_dirawat', 'pernah_tindakan', 'r_alergi', 'transfusi', 'is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asesmenawal_id' => 'Asesmenawal ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'waktu_tiba' => 'Waktu Tiba',
            'asal_masuk' => 'Asal Masuk',
            'tgl_asesmen' => 'Tgl Asesmen',
            'asesmen_diambildari' => 'Asesmen Diambildari',
            'diambildari_nama' => 'Diambildari Nama',
            'diambildari_hub' => 'Diambildari Hub',
            'masuk_dengan' => 'Masuk Dengan',
            'masuk_denganlain' => 'Masuk Denganlain',
            'obat_darirumah' => 'Obat Darirumah',
            'obatan_rumah' => 'Obatan Rumah',
            'hasil_pemeriksaan' => 'Hasil Pemeriksaan',
            'hasil_rad' => 'Hasil Rad',
            'hasil_lab' => 'Hasil Lab',
            'hasil_lainnya' => 'Hasil Lainnya',
            'keluhan_utama' => 'Keluhan Utama',
            'diagnosa_masuk' => 'Diagnosa Masuk',
            'r_kehamilan_g' => 'R Kehamilan G',
            'r_kehamilan_p' => 'R Kehamilan P',
            'r_kehamilan_a' => 'R Kehamilan A',
            'hpht' => 'Hpht',
            'haid_teratur' => 'Haid Teratur',
            'ketergantungan' => 'Ketergantungan',
            'r_penyakit_kel' => 'R Penyakit Kel',
            'penyakit_kel_lain' => 'Penyakit Kel Lain',
            'r_kes_sekarang' => 'R Kes Sekarang',
            'pernah_dirawat' => 'Pernah Dirawat',
            'tgl_dirawat' => 'Tgl Dirawat',
            'alasan_dirawat' => 'Alasan Dirawat',
            'pernah_tindakan' => 'Pernah Tindakan',
            'tgl_tindakan' => 'Tgl Tindakan',
            'alasan_tindakan' => 'Alasan Tindakan',
            'jeniskegiatantindakan_id' => 'Jeniskegiatantindakan ID',
            'r_alergi' => 'R Alergi',
            'nama_alergi' => 'Nama Alergi',
            'transfusi' => 'Transfusi',
            'reaksi' => 'Reaksi',
            'asmen_riwayat' => 'Asmen Riwayat',
            'asmen_periksafisik' => 'Asmen Periksafisik',
            'asmen_keb_dasar' => 'Asmen Keb Dasar',
            'asmen_sk_fungsional' => 'Asmen Sk Fungsional',
            'asmen_sk_gizi' => 'Asmen Sk Gizi',
            'asmen_keb_pendidikan' => 'Asmen Keb Pendidikan',
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

    public function mappingDataAttributes($attributes)
    {
        $additional_existing = is_array($this->additional_data) ? $this->additional_data : json_decode($this->additional_data, true);
        $additional = array_diff_key($attributes, $this->attributes);
        if (is_array($additional_existing)) {
            $additional = array_merge($additional_existing, $additional);
        }

        foreach ($additional as $key => $value) {
            if (empty($additional[$key]) && $additional[$key] !== '0') {
                unset($additional[$key]);
            }
        }
        $attributes['additional_data'] = json_encode($additional);

        $mappingAttributes = [
            'waktu_tiba' => 'tgl_datang',
            'keluhan_utama' => 'keluhan',
            'diagnosa_masuk' => 'diagnosa_keperawatan',
            'r_kes_sekarang' => 'r_penyakitsaatini',
            'r_alergi' => 'is_alergi',
        ];

        foreach ($mappingAttributes as $to => $from) {
            $attributes[$to] = $attributes[$from];
        }

        foreach ($this->primaryKey() as $key) {
            if (isset($attributes[$key])) {
                unset($attributes[$key]);
            }
        }
        $this->attributes = $attributes;
    }

    public function getArrayAttributes()
    {
        $additional_existing = is_array($this->additional_data) ? $this->additional_data : json_decode($this->additional_data, true);
        $attributes = $this->attributes;
        if (is_array($additional_existing)) {
            $attributes = array_merge($attributes, $additional_existing);
        }
        return $attributes;
    }

    public static function getStaticAdditional($array)
    {
        $additional_existing = is_array($array['additional_data']) ? $array['additional_data'] : json_decode($array['additional_data'], true);
        $attributes = $array;
        if (is_array($additional_existing)) {
            $attributes = array_merge($attributes, $additional_existing);
        }
        unset($attributes['additional_data']);
        return $attributes;
    }
}
