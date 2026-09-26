<?php

namespace app\modules\v1\models;

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
    
    public $propinsi_id;
    public $propinsi_nama;
    public $propinsi_namalainnya;
    public $kode_propinsi;
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
            'propinsi_id' => \Yii::t('app','Propinsi'),
            'propinsi_nama' => \Yii::t('app','Nama propinsi'),
            'propinsi_namalainnya' => \Yii::t('app','Nama lainnya'),
            'kode_propinsi' => \Yii::t('app','Kode propinsi'),
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
