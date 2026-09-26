<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "persetujuanjenazah_t".
 *
 * @property int $persetujuanjenazah_id
 * @property int $pasien_id
 * @property int $ruangan_id ruangan meninggal
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $kondisi
 * @property int $hubungan_keluarga lookup_type='hubungan_keluarga'
 * @property string $nama_pj
 * @property int $jeniskelamin_id lookup_type='jenis_kelamin'
 * @property int $umur
 * @property int $no_kontak
 * @property string $alamat
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
class AmbiljenazahT extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persetujuanjenazah_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'ruangan_id', 'pendaftaran_id', 'hubungan_keluarga', 'nama_pj'], 'required'],
            [['pasien_id', 'ruangan_id', 'pendaftaran_id', 'pasienadmisi_id', 'hubungan_keluarga', 'jeniskelamin_id', 'umur', 'no_kontak', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'ruangan_id', 'pendaftaran_id', 'pasienadmisi_id', 'hubungan_keluarga', 'jeniskelamin_id', 'umur', 'no_kontak', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kondisi', 'alamat'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_pj'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'persetujuanjenazah_id' => 'Persetujuanjenazah ID',
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'kondisi' => 'Kondisi',
            'hubungan_keluarga' => 'Hubungan Keluarga',
            'nama_pj' => 'Nama Pj',
            'jeniskelamin_id' => 'Jeniskelamin ID',
            'umur' => 'Umur',
            'no_kontak' => 'No Kontak',
            'alamat' => 'Alamat',
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
