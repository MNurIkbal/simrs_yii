<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "formsobarangdetail_t".
 *
 * @property int $formsobarangdetail_id
 * @property int $stokopnamebarangdetail_id
 * @property int $barang_id
 * @property int $formsobarang_id
 * @property double $stok
 * @property double $harganetto
 * @property int $periodestok_id
 * @property string $nobatch
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
 * @property int $ruangan_id
 */
class FormSoBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formsobarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'periodestok_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangan_id'], 'default', 'value' => null],
            [['stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'periodestok_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangan_id'], 'integer'],
            [['stok', 'harganetto'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','stokbarang_id','is_newso'], 'safe'],
            [['is_deleted', 'is_active', 'is_newso'], 'boolean'],
            [['nobatch'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarangdetail_id' => 'Formsobarangdetail ID',
            'stokopnamebarangdetail_id' => 'Stokopnamebarangdetail ID',
            'barang_id' => 'Barang ID',
            'formsobarang_id' => 'Formsobarang ID',
            'stok' => 'Stok',
            'harganetto' => 'Harganetto',
            'periodestok_id' => 'Periodestok ID',
            'nobatch' => 'Nobatch',
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
            'ruangan_id' => 'Ruangan ID',
        ];
    }
}
