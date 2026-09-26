<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "layarantriandetail_m".
 *
 * @property int $layarantriandetail_id
 * @property int $layarantrian_id
 * @property int $ruangan_id
 * @property int $pegawai_id
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
class LayarAntrianDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'layarantriandetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['layarantrian_id'], 'required'],
            [['layarantrian_id', 'ruangan_id', 'pegawai_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['layarantrian_id', 'ruangan_id', 'pegawai_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['layarantriandetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'layarantriandetail_id' => 'Layarantriandetail ID',
            'layarantrian_id' => 'Layarantrian ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
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
