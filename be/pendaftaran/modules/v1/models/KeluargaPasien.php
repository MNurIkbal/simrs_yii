<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "keluargapasien_t".
 *
 * @property int $keluargapasien_id
 * @property string $keluarga_nama
 * @property string $keluarga_jk
 * @property string $keluarga_hubungan
 * @property string $keluarga_alamat
 * @property string $keluarga_no_telepon
 * @property string $keluarga_namadepan
 * @property string $keluarga_propinsi_id
 * @property string $keluarga_kabupaten_id
 * @property string $keluarga_kecamatan_id
 * @property string $keluarga_kelurahan_id
 * @property string $keluarga_pekerjaan_id
 * @property string $keluarga_rt
 * @property string $keluarga_rw
 * @property string $pasien_id
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $pasien_id
 */
class KeluargaPasien extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'keluarga_nama', 
        'keluarga_jk',
        'keluarga_hubungan',
        'keluarga_alamat',
        'keluarga_no_telepon',
        'keluarga_namadepan',
        'keluarga_propinsi_id',
        'keluarga_kabupaten_id',
        'keluarga_kecamatan_id',
        'keluarga_kelurahan_id',
        'keluarga_pekerjaan_id',
        'keluarga_rt',
        'keluarga_rw',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'keluargapasien_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['keluarga_jk', 'keluarga_hubungan', 'keluarga_alamat', 'keluarga_no_telepon', 'keluarga_namadepan',
                'keluarga_propinsi_id', 'keluarga_kabupaten_id', 'keluarga_kecamatan_id', 'keluarga_kelurahan_id',
                'keluarga_pekerjaan_id', 'keluarga_rt', 'keluarga_rw', 'alamatdepan',
                'pasien_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keluarga_alamat'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['keluarga_nama', 'keluarga_hubungan', 'keluarga_namadepan'], 'string', 'max' => 50],
            [['keluarga_jk'], 'string', 'max' => 20],
            [['keluarga_no_telepon'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'keluargapasien_id' => 'KeluargaPasien ID',
            'keluarga_nama' => 'Nama',
            'keluarga_jk' => 'Jenis kelamin',
            'keluarga_hubungan' => 'Hubungan',
            'keluarga_alamat'=> 'Alamat',
            'keluarga_no_telepon'=> 'No telepon',
            'is_pj'=> 'Penangung Jawab',
            'keluarga_namadepan' => 'Nama Depan',
            'keluarga_propinsi_id' => 'Propinsi',
            'keluarga_kabupaten_id' => 'Kabupaten/Kota',
            'keluarga_kecamatan_id'=> 'Kecamatan',
            'keluarga_kelurahan_id'=> 'Kelurahan',
            'keluarga_pekerjaan_id'=> 'Pekerjaan',
            'keluarga_rt'=> 'RT',
            'keluarga_rw'=> 'RW',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'pasien_id' => 'Pasien ID',
        ];
    }
}
