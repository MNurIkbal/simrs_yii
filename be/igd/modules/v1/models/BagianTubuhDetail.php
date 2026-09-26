<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bagiantubuhdetail_m".
 *
 * @property int $bagiantubuhdetail_id
 * @property int $bagiantubuh_id
 * @property string $nama_bagiantubuh
 * @property string $nama_lainnya
 * @property string $kordinat_x
 * @property string $kordinat_y
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
class BagianTubuhDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bagiantubuhdetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bagiantubuhdetail_id', 'bagiantubuh_id'], 'required'],
            [['bagiantubuhdetail_id', 'bagiantubuh_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['bagiantubuhdetail_id', 'bagiantubuh_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_bagiantubuh', 'nama_lainnya', 'kordinat_x', 'kordinat_y'], 'string', 'max' => 255],
            [['bagiantubuhdetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bagiantubuhdetail_id' => 'Bagiantubuhdetail ID',
            'bagiantubuh_id' => 'Bagiantubuh ID',
            'nama_bagiantubuh' => 'Nama Bagiantubuh',
            'nama_lainnya' => 'Nama Lainnya',
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
