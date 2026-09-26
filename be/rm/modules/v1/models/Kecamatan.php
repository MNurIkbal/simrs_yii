<?php

namespace app\modules\v1\models;

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
    
    public $kecamatan_id;
    public $kabupaten_id;
    public $kecamatan_nama;
    public $kecamatan_namalainnya;
    public $longitude;
    public $latitude;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kecamatan_id' => \Yii::t('app','Kecamatan'),
            'kabupaten_id' => \Yii::t('app','Kabupaten'),
            'kecamatan_nama' => \Yii::t('app','Nama kecamatan'),
            'kecamatan_namalainnya' => \Yii::t('app','Nama lainnya'),
            'longitude' => \Yii::t('app','Longitude'),
            'latitude' => \Yii::t('app','Latitude'),
            'additional_data' => \Yii::t('app','Additional data'),
            'created_date' => \Yii::t('app','Created date'),
            'created_by' => \Yii::t('app','Created by'),
            'modified_count' => \Yii::t('app','Modified count'),
            'last_modified_date' => \Yii::t('app','Last modified date'),
            'last_modified_by' => \Yii::t('app','Last modified by'),
            'is_deleted' => \Yii::t('app','Is deleted'),
            'is_active' => \Yii::t('app','Status'),
            'deleted_date' => \Yii::t('app','Deleted date'),
            'deleted_by' => \Yii::t('app','Deleted by'),
        ];
    }
}
