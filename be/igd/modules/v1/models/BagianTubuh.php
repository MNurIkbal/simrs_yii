<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bagiantubuh_m".
 *
 * @property int $bagiantubuh_id
 * @property string $namabagtubuh
 * @property string $bagtubuh_namalain
 * @property double $kordinat_x
 * @property double $kordinat_y
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
class BagianTubuh extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bagiantubuh_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['namabagtubuh', 'bagtubuh_namalain', 'kordinat_x', 'kordinat_y'], 'required'],
            [['kordinat_x', 'kordinat_y'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['namabagtubuh', 'bagtubuh_namalain'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bagiantubuh_id' => 'Bagiantubuh ID',
            'namabagtubuh' => 'Namabagtubuh',
            'bagtubuh_namalain' => 'Bagtubuh Namalain',
            'kordinat_x' => 'Kordinat X',
            'kordinat_y' => 'Kordinat Y',
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
