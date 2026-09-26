<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "kabupaten_m".
 *
 * @property integer $kabupaten_id
 * @property integer $propinsi_id
 * @property string $kabupaten_nama
 * @property string $kabupaten_namalainnya
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
class Kabupaten extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kabupaten_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['propinsi_id', 'kabupaten_nama'], 'required'],
            [['propinsi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['longitude', 'latitude', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kabupaten_nama', 'kabupaten_namalainnya'], 'string', 'max' => 50],
            [['propinsi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Propinsi::className(), 'targetAttribute' => ['propinsi_id' => 'propinsi_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kabupaten_id' => 'Kabupaten ID',
            'propinsi_id' => 'Propinsi',
            'kabupaten_nama' => 'Nama Kabupaten/Kota',
            'kabupaten_namalainnya' => 'Nama Lainnya',
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
    
    public function getPropinsi()
    {
        return $this->hasOne(Propinsi::className(), ['propinsi_id' => 'propinsi_id']);
    }
    
    public function extraFields()
    {
        return ['propinsi_m' => function($item){
            return $item->propinsi;
        }];
    }
}
