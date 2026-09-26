<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "formstokopname_t".
 *
 * @property int $formstokopname_id
 * @property int $stokopnamedetail_id
 * @property int $obatalkes_id
 * @property int $formulirstokopname_id
 * @property double $volume_stok
 * @property int $periodestok_id
 * @property int $ruangan_id
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
 * @property string $nobatch
 */
class FormStokOpname extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formstokopname_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id'], 'required'],
            [['formstokopname_id', 'stokopnamedetail_id', 'obatalkes_id', 'formulirstokopname_id', 'periodestok_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','is_newso'], 'default', 'value' => null],
            [['formstokopname_id', 'stokopnamedetail_id', 'obatalkes_id', 'formulirstokopname_id', 'periodestok_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['volume_stok'], 'number'],
            [['additional_data'], 'string'],
            [['created_date','tglkadaluarsa', 'last_modified_date', 'deleted_date', 'stokobatalkes_id', 'is_newso'], 'safe'],
            [['is_deleted', 'is_active', 'is_newso'], 'boolean'],
            [['nobatch'], 'string', 'max' => 100],
            [['formstokopname_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formstokopname_id' => 'Formstokopname ID',
            'stokopnamedetail_id' => 'Stokopnamedetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'volume_stok' => 'Volume Stok',
            'periodestok_id' => 'Periodestok ID',
            'ruangan_id' => 'Ruangan ID',
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
            'nobatch' => 'Nobatch',
        ];
    }
}
