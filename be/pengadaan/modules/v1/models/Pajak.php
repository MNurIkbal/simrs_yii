<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pajak_m".
 *
 * @property int $pajak_id
 * @property string $pajak_kode
 * @property string $pajak_name
 * @property int $pajak_persen
 * @property int $akunmasuk_id
 * @property int $akunkeluar_id
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
class Pajak extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pajak_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pajak_id'], 'required'],
            [['pajak_id', 'pajak_persen', 'akunmasuk_id', 'akunkeluar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pajak_id', 'pajak_persen', 'akunmasuk_id', 'akunkeluar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pajak_kode'], 'string', 'max' => 50],
            [['pajak_name'], 'string', 'max' => 255],
            [['pajak_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pajak_id' => 'Pajak ID',
            'pajak_kode' => 'Pajak Kode',
            'pajak_name' => 'Pajak Name',
            'pajak_persen' => 'Pajak Persen',
            'akunmasuk_id' => 'Akunmasuk ID',
            'akunkeluar_id' => 'Akunkeluar ID',
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
