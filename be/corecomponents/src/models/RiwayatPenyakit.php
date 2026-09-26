<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 11:41:38
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-15 11:41:43
 * @Description: 
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "lookup_m".
 *
 * @property integer $lookup_id
 * @property string $lookup_type
 * @property string $lookup_name
 * @property string $lookup_value
 * @property integer $lookup_urutan
 * @property string $lookup_kode
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
 */
class RiwayatPenyakit extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'riwayat_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riwayat_nama'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['riwayat_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riwayat_id' => 'Riwayat ID',
            'riwayat_nama' => 'Nama Riwayat',
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
