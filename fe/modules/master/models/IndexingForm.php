<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "indexing_m".
 *
 * @property integer $indexing_id
 * @property integer $kelompokremunerasi_id
 * @property integer $indexing_urutan
 * @property string $indexing_nama
 * @property string $indexing_singk
 * @property double $indexing_nilai
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
class IndexingForm extends \yii\base\Model
{
    
    public $indexing_id;
    public $kelompokremunerasi_id;
    public $indexing_urutan;
    public $indexing_nama;
    public $indexing_singk;
    public $indexing_nilai;
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
        return 'indexing_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokremunerasi_id', 'indexing_urutan', 'indexing_nama', 'indexing_singk', 'indexing_nilai'], 'required'],
            [['kelompokremunerasi_id', 'indexing_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['indexing_nilai'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['indexing_nama'], 'string', 'max' => 100],
            [['indexing_singk'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'indexing_id' => \Yii::t('fe','Indexing'),
            'kelompokremunerasi_id' => \Yii::t('fe','Kelompok remunerasi'),
            'indexing_urutan' => \Yii::t('fe','Urutan Indexing'),
            'indexing_nama' => \Yii::t('fe','Nama'),
            'indexing_singk' => \Yii::t('fe','Singkatan'),
            'indexing_nilai' => \Yii::t('fe','Nilai'),
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
