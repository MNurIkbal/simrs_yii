<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "permintaanmakandetail_t".
 *
 * @property int $permintaanmakandetail_id
 * @property int $permintaanmakan_id
 * @property int $jenisdiet_id
 * @property int $makanandiet_id
 * @property int $waktu_diet lookup_m.lookup_type='waktu'
 * @property int $jumlah
 * @property string $keterangan
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
class PermintaanMakanDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaanmakandetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaanmakan_id', 'jenisdiet_id', 'makanandiet_id', 'jumlah'], 'required'],
            [['permintaanmakan_id', 'jenisdiet_id', 'makanandiet_id', 'waktu_diet', 'jumlah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['permintaanmakan_id', 'jenisdiet_id', 'makanandiet_id', 'waktu_diet', 'jumlah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['keterangan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaanmakandetail_id' => 'Permintaanmakandetail ID',
            'permintaanmakan_id' => 'Permintaanmakan ID',
            'jenisdiet_id' => 'Jenisdiet ID',
            'makanandiet_id' => 'Makanandiet ID',
            'waktu_diet' => 'Waktu Diet',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
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
