<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "riwayatkunjungan_r".
 *
 * @property int $riwayatkunjungan_id
 * @property int $pendaftaranold_id
 * @property string $tgl_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tipe_pendaftaran
 * @property string $status_pendaftaran
 * @property string $instalasi_nama
 * @property string $kelaspelayanan_nama
 * @property int $dokter_id
 * @property string $nama_dokter
 * @property string $dokter_internal_perujuk
 * @property string $diagnosa_perujuk
 * @property string $terapi_rekomendasi_perujuk
 * @property string $dokter_perujuk
 * @property string $catatan_perujuk
 * @property string $prosedur_masuk
 * @property bool $is_bolehpulang
 * @property string $tgl_pulang
 * @property string $nama_perujuk
 * @property string $alamat_perujuk
 * @property bool $is_perjanjian
 * @property string $nama_asuransi
 * @property string $kamar
 * @property string $no_tempat_tidur
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
class RiwayatkunjunganR extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'riwayatkunjungan_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaranold_id'], 'required'],
            [['pendaftaranold_id', 'pasien_id', 'dokter_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaranold_id', 'pasien_id', 'dokter_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pendaftaran', 'tgl_pulang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['nama_dokter', 'dokter_internal_perujuk', 'diagnosa_perujuk', 'terapi_rekomendasi_perujuk', 'dokter_perujuk', 'catatan_perujuk', 'nama_perujuk', 'alamat_perujuk', 'additional_data'], 'string'],
            [['is_bolehpulang', 'is_perjanjian', 'is_deleted', 'is_active'], 'boolean'],
            [['no_rekam_medik'], 'string', 'max' => 20],
            [['nama_pasien'], 'string', 'max' => 150],
            [['tipe_pendaftaran', 'status_pendaftaran', 'instalasi_nama', 'kelaspelayanan_nama', 'prosedur_masuk'], 'string', 'max' => 50],
            [['nama_asuransi', 'kamar'], 'string', 'max' => 64],
            [['no_tempat_tidur'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'riwayatkunjungan_id' => 'Riwayatkunjungan ID',
            'pendaftaranold_id' => 'Pendaftaranold ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tipe_pendaftaran' => 'Tipe Pendaftaran',
            'status_pendaftaran' => 'Status Pendaftaran',
            'instalasi_nama' => 'Instalasi Nama',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'dokter_id' => 'Dokter ID',
            'nama_dokter' => 'Nama Dokter',
            'dokter_internal_perujuk' => 'Dokter Internal Perujuk',
            'diagnosa_perujuk' => 'Diagnosa Perujuk',
            'terapi_rekomendasi_perujuk' => 'Terapi Rekomendasi Perujuk',
            'dokter_perujuk' => 'Dokter Perujuk',
            'catatan_perujuk' => 'Catatan Perujuk',
            'prosedur_masuk' => 'Prosedur Masuk',
            'is_bolehpulang' => 'Is Bolehpulang',
            'tgl_pulang' => 'Tgl Pulang',
            'nama_perujuk' => 'Nama Perujuk',
            'alamat_perujuk' => 'Alamat Perujuk',
            'is_perjanjian' => 'Is Perjanjian',
            'nama_asuransi' => 'Nama Asuransi',
            'kamar' => 'Kamar',
            'no_tempat_tidur' => 'No Tempat Tidur',
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
