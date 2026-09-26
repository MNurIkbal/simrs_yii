<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "marginkhususdetail_k".
 *
 * @property int $marginkhususdetail_id
 * @property int $marginkhusus_id
 * @property double $jenisobat_id
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
class MarginKhususDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'marginkhususdetail_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['marginkhusus_id'], 'required'],
            [['marginkhusus_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['marginkhusus_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jenisobat_id'], 'number'],
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
            'marginkhususdetail_id' => 'Margin Khusus Detail ID',
            'marginkhusus_id' => 'Margin Khusus ID',
            'jenisobat_id' => 'Jenis Obat',
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

    public function getJenisobatalkes()
    {
        return $this->hasOne(JenisObatAlkes::className(), ['jenisobatalkes_id' => 'jenisobat_id']);
    }

    public function extraFields()
    {
        return [
            'jenisobatalkes_m' => function($item){
                return $item->jenisobatalkes;
            }
        ];
    }
}
