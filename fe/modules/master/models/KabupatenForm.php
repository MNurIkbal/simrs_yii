<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kabupaten_m".
 *
 * @property integer $kabupaten_id
 * @property integer $kabupaten_id
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
class KabupatenForm extends \yii\base\Model
{
    
    public $kabupaten_id;
    public $propinsi_id;
    public $kabupaten_nama;
    public $kabupaten_namalainnya;
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kabupaten_id' => \Yii::t('fe','Kabupaten'),
            'propinsi_id' => \Yii::t('fe','Propinsi'),
            'kabupaten_nama' => \Yii::t('fe','Nama kabupaten'),
            'kabupaten_namalainnya' => \Yii::t('fe','Nama lainnya'),
            'longitude' => \Yii::t('fe','Longitude'),
            'latitude' => \Yii::t('fe','Latitude'),
            'additional_data' => \Yii::t('fe','Additional data'),
            'created_date' => \Yii::t('fe','Created date'),
            'created_by' => \Yii::t('fe','Created by'),
            'modified_count' => \Yii::t('fe','Modified count'),
            'last_modified_date' => \Yii::t('fe','Last modified date'),
            'last_modified_by' => \Yii::t('fe','Last modified by'),
            'is_deleted' => \Yii::t('fe','Is deleted'),
            'is_active' => \Yii::t('fe','Status'),
            'deleted_date' => \Yii::t('fe','Deleted date'),
            'deleted_by' => \Yii::t('fe','Deleted by'),
        ];
    }
}
