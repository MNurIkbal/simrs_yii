<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "propinsi_m".
 *
 * @property integer $propinsi_id
 * @property string $propinsi_nama
 * @property string $propinsi_namalainnya
 * @property string $kode_propinsi
 * @property string $longitude
 * @property string $latitude
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
class Propinsi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'propinsi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['propinsi_nama'], 'required'],
            [['longitude', 'latitude', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['propinsi_nama', 'propinsi_namalainnya'], 'string', 'max' => 50],
            [['kode_propinsi'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'propinsi_id' => 'ID',
            'propinsi_nama' => 'Nama',
            'propinsi_namalainnya' => 'Nama Lainnya',
            'kode_propinsi' => 'Kode Propinsi',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
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
