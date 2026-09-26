<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "historijenazah_r".
 *
 * @property int $historijenazah_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $persetujuanjenazah_id
 * @property int $pasienmasukpenunjang_id
 * @property int $ambiljenazah_id
 * @property string $tgl_pelayanan
 * @property int $pegawai_id
 * @property int $status lookup_type='status_periksa_penunjang'
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
class HistoriJenazah extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historijenazah_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pendaftaran_id', 'tgl_pelayanan', 'pegawai_id', 'status'], 'required'],
            [['pasien_id', 'pendaftaran_id', 'pasienadmisi_id', 'persetujuanjenazah_id', 'pasienmasukpenunjang_id', 'ambiljenazah_id', 'pegawai_id', 'status', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['historijenazah_id', 'pasien_id', 'pendaftaran_id', 'pasienadmisi_id', 'persetujuanjenazah_id', 'pasienmasukpenunjang_id', 'ambiljenazah_id', 'pegawai_id', 'status', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pelayanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'historijenazah_id' => 'Historijenazah ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'persetujuanjenazah_id' => 'Persetujuanjenazah ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'ambiljenazah_id' => 'Ambiljenazah ID',
            'tgl_pelayanan' => 'Tgl Pelayanan',
            'pegawai_id' => 'Pegawai ID',
            'status' => 'Status',
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
