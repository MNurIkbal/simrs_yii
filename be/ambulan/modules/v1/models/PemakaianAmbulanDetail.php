<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemakaianambulandetail_t".
 *
 * @property int $pemakaianambulandetail_id
 * @property int $pemakaianambulan_id
 * @property int $petugas_id
 * @property int $obatalkes_id
 * @property int $qty

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
class PemakaianAmbulanDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemakaianambulandetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['pemakaianambulan_id'], 'required'],
            [[  'pemakaianambulan_id',
                'petugas_id', 
                'obatalkes_id', 
                'qty',
                'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pemakaianambulan_id', 
                'petugas_id', 
                'obatalkes_id', 
                'qty',
                'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
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
            'pemakaianambulandetail_id' => 'Pemakaian Ambulan Detail ID',
            'pemakaianambulan_id' => 'Pemakaian Ambulan ID',
            'petugas_id' => 'Petugas ID',
            'obatalkes_id' => 'Obat Alkes ID',
            'qty' => 'Qty',

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
