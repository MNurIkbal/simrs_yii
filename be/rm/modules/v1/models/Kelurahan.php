<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelurahan_m".
 *
 * @property integer $kelurahan_id
 * @property integer $kecamatan_id
 * @property string $kelurahan_nama
 * @property string $kelurahan_namalainnya
 * @property string $kode_pos
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
class Kelurahan extends \Doco\components\DocoActiveRecord
{
    
    public $kelurahan_id;
    public $kecamatan_id;
    public $kelurahan_nama;
    public $kelurahan_namalainnya;
    public $kode_pos;
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
        return 'kelurahan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kecamatan_id', 'kelurahan_nama'], 'required'],
            [['kecamatan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['longitude', 'latitude', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelurahan_nama', 'kelurahan_namalainnya'], 'string', 'max' => 50],
            [['kode_pos'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelurahan_id' => \Yii::t('app','Kelurahan'),
            'kecamatan_id' => \Yii::t('app','Kecamatan'),
            'kelurahan_nama' => \Yii::t('app','Nama kelurahan'),
            'kelurahan_namalainnya' => \Yii::t('app','Nama lainnya'),
            'kode_pos' => \Yii::t('app','Kode pos'),
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
