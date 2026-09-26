<?php

namespace app\modules\v1\models;

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
class Jabatan extends \Doco\components\DocoActiveRecord
{
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
            'jabatan_id' => \Yii::t('app','Jabatan'),
            'kelompokjabatan_id' => \Yii::t('app','Kelompok jabatan'),
            'indexing_id' => \Yii::t('app','Indexing'),
            'jabatan_urutan' => \Yii::t('app','Urutan'),
            'jabatan_nama' => \Yii::t('app','Nama jabatan'),
            'jabatan_lainnya' => \Yii::t('app','Nama lainnya'),
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
