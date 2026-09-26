<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "pangkat_m".
 *
 * @property integer $pangkat_id
 * @property integer $golonganpegawai_id
 * @property integer $pangkat_urutan
 * @property string $pangkat_nama
 * @property string $pangkat_namalainnya
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
class PangkatForm extends \yii\base\Model
{
    
    public $pangkat_id;
    public $golonganpegawai_id;
    public $pangkat_urutan;
    public $pangkat_nama;
    public $pangkat_namalainnya;
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
        return 'pangkat_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['golonganpegawai_id', 'pangkat_urutan', 'pangkat_nama'], 'required'],
            [['golonganpegawai_id', 'pangkat_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pangkat_nama', 'pangkat_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pangkat_id' => \Yii::t('fe','Pangkat'),
            'golonganpegawai_id' => \Yii::t('fe','Golongan pegawai'),
            'pangkat_urutan' => \Yii::t('fe','Urutan'),
            'pangkat_nama' => \Yii::t('fe','Nama pangkat'),
            'pangkat_namalainnya' => \Yii::t('fe','Nama lainnya'),
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
