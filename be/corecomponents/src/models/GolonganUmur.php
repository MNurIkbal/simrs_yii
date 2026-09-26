<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "golonganumur_m".
 *
 * @property integer $golonganumur_id
 * @property string $golonganumur_nama
 * @property string $golonganumur_namalainnya
 * @property string $golonganumur_minimal
 * @property string $golonganumur_maksimal
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property PendaftaranT[] $pendaftaranTs
 */
class GolonganUmur extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'golonganumur_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['golonganumur_nama', 'golonganumur_namalainnya'], 'required'],
            [['golonganumur_minimal', 'golonganumur_maksimal'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['golonganumur_nama', 'golonganumur_namalainnya'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'golonganumur_id' => 'Golonganumur ID',
            'golonganumur_nama' => 'Golonganumur Nama',
            'golonganumur_namalainnya' => 'Golonganumur Namalainnya',
            'golonganumur_minimal' => 'Golonganumur Minimal',
            'golonganumur_maksimal' => 'Golonganumur Maksimal',
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
