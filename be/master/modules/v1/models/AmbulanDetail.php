<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ambulandetail_m".
 *
 * @property int $ambulandetail_id
 * @property int $ambulan_id
 * @property int $daftartindakan_id
 * @property bool $is_default
 * @property int $obatalkes_id
 * @property int $qty
 * @property int $satuankonversi_id
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
class AmbulanDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ambulandetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambulan_id'], 'required'],
            [['ambulan_id', 'daftartindakan_id', 'obatalkes_id', 'qty', 'satuankonversi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ambulan_id', 'daftartindakan_id', 'obatalkes_id', 'qty', 'satuankonversi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_default', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambulandetail_id' => 'Ambulandetail ID',
            'ambulan_id' => 'Ambulan ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'is_default' => 'Is Default',
            'obatalkes_id' => 'Obatalkes ID',
            'qty' => 'Qty',
            'satuankonversi_id' => 'Satuankonversi ID',
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
