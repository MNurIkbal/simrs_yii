<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigmargindetail_k".
 *
 * @property int $konfigmargindetail_id
 * @property int $konfigmargin_id
 * @property double $harga_min
 * @property double $harga_max
 * @property double $margin
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
class KonfigMarginDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigmargindetail_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['konfigmargin_id'], 'required'],
            [['konfigmargin_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['konfigmargin_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['harga_min', 'harga_max'], 'number'],
            [['additional_data'], 'string'],
            [['margin', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigmargindetail_id' => 'Konfigmargindetail ID',
            'konfigmargin_id' => 'Konfigmargin ID',
            'harga_min' => 'Harga Min',
            'harga_max' => 'Harga Max',
            'margin' => 'Margin',
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
