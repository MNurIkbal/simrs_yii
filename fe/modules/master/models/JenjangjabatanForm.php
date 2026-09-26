<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jenjangjabatan_m".
 *
 * @property integer $jenjangjabatan_id
 * @property integer $indexing_id
 * @property integer $jenisjabatan_id
 * @property string $jenjangjabatan_nama
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
class JenjangJabatanForm extends \yii\base\Model
{
    
    public $jenjangjabatan_id;
    public $indexing_id;
    public $jenisjabatan_id;
    public $jenjangjabatan_nama;
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
        return 'jenjangjabatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenjangjabatan_id'], 'required'],
            [['jenjangjabatan_id', 'indexing_id', 'jenisjabatan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenjangjabatan_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenjangjabatan_id' => \Yii::t('fe','Jenjang jabatan'),
            'indexing_id' => \Yii::t('fe','Indexing'),
            'jenisjabatan_id' => \Yii::t('fe','Jenis jabatan'),
            'jenjangjabatan_nama' => \Yii::t('fe','Nama jenjang jabatan'),
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
