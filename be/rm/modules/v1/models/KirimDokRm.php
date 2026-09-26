<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kirimdokrm_t".
 *
 * @property int $kirimdokrm_id
 * @property int $pesandokrm_id
 * @property int $ruanganpengirim_id
 * @property int $ruanganpemesan_id
 * @property string $no_kirimdokrm
 * @property string $tgl_kirim
 * @property int $pegawaipengirim_id
 * @property int $status_kirim lookup_type='status_kirim'
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
class KirimDokRm extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kirimdokrm_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandokrm_id', 'ruanganpengirim_id', 'ruanganpemesan_id', 'tgl_kirim', 'pegawaipengirim_id'], 'required'],
            [['pesandokrm_id', 'ruanganpengirim_id', 'ruanganpemesan_id', 'pegawaipengirim_id', 'status_kirim', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandokrm_id', 'ruanganpengirim_id', 'ruanganpemesan_id', 'pegawaipengirim_id', 'status_kirim', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_kirimdokrm'], 'string', 'max' => 255],
            [['no_kirimdokrm'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kirimdokrm_id' => 'Kirimdokrm ID',
            'pesandokrm_id' => 'Pesandokrm ID',
            'ruanganpengirim_id' => 'Ruanganpengirim ID',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'no_kirimdokrm' => 'No Kirimdokrm',
            'tgl_kirim' => 'Tgl Kirim',
            'pegawaipengirim_id' => 'Pegawaipengirim ID',
            'status_kirim' => 'Status Kirim',
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
