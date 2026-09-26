<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompokjabatan_m".
 *
 * @property integer $kelompokjabatan_id
 * @property string $kelompokjabatan_nama
 * @property string $kelompokjabatan_namalainnya
 * @property string $kelompokjabatan_fungsi
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
class KelompokJabatanForm extends \yii\base\Model
{
    
    public $kelompokjabatan_id;
    public $kelompokjabatan_nama;
    public $kelompokjabatan_namalainnya;
    public $kelompokjabatan_fungsi;
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
        return 'kelompokjabatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokjabatan_nama'], 'required'],
            [['kelompokjabatan_fungsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokjabatan_nama', 'kelompokjabatan_namalainnya'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokjabatan_id' => \Yii::t('fe','Kelompok jabatan'),
            'kelompokjabatan_nama' => \Yii::t('fe','Kelompok jabatan'),
            'kelompokjabatan_namalainnya' => \Yii::t('fe','Nama lainnya'),
            'kelompokjabatan_fungsi' => \Yii::t('fe','Fungsi'),
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
