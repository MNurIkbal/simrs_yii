<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jabatan_m".
 *
 * @property integer $jabatan_id
 * @property integer $kelompokjabatan_id
 * @property integer $indexing_id
 * @property integer $jabatan_urutan
 * @property string $jabatan_nama
 * @property string $jabatan_lainnya
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
class JabatanForm extends \yii\base\Model
{
    
    public $jabatan_id;
    public $kelompokjabatan_id;
    public $indexing_id;
    public $jabatan_urutan;
    public $jabatan_nama;
    public $jabatan_lainnya;
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
        return 'jabatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokjabatan_id', 'indexing_id', 'jabatan_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jabatan_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jabatan_nama', 'jabatan_lainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jabatan_id' => \Yii::t('fe','Jabatan'),
            'kelompokjabatan_id' => \Yii::t('fe','Kelompok jabatan'),
            'indexing_id' => \Yii::t('fe','Indexing'),
            'jabatan_urutan' => \Yii::t('fe','Urutan'),
            'jabatan_nama' => \Yii::t('fe','Nama jabatan'),
            'jabatan_lainnya' => \Yii::t('fe','Nama lainnya'),
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
