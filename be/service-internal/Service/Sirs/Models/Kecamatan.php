<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "kecamatan_m".
 *
 * @property integer $kecamatan_id
 * @property integer $kabupaten_id
 * @property string $kecamatan_nama
 * @property string $kecamatan_namalainnya
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
class Kecamatan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kecamatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kabupaten_id', 'kecamatan_nama'], 'required'],
            [['kabupaten_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['longitude', 'latitude', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kecamatan_nama', 'kecamatan_namalainnya'], 'string', 'max' => 50],
            [['kabupaten_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kabupaten::className(), 'targetAttribute' => ['kabupaten_id' => 'kabupaten_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kecamatan_id' => 'Kecamatan ID',
            'kabupaten_id' => 'Kabupaten',
            'kecamatan_nama' => 'Nama Kecamatan',
            'kecamatan_namalainnya' => 'Nama Lainnya',
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
    
    public function getKabupaten()
    {
        return $this->hasOne(Kabupaten::className(), ['kabupaten_id' => 'kabupaten_id']);
    }
    
    public function extraFields()
    {
        return ['kabupaten_m' => function($item){
            return $item->kabupaten;
        }];
    }
}
