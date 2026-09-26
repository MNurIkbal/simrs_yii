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
            [['indexing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Indexing::className(), 'targetAttribute' => ['indexing_id' => 'indexing_id']],
            [['kelompokjabatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelompokjabatan::className(), 'targetAttribute' => ['kelompokjabatan_id' => 'kelompokjabatan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jabatan_id' => 'Jabatan ID',
            'kelompokjabatan_id' => 'Kelompok Jabatan',
            'indexing_id' => 'Indexing',
            'jabatan_urutan' => 'Urutan',
            'jabatan_nama' => 'Nama Jabatan',
            'jabatan_lainnya' => 'Nama Lainnya',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
    
    public function getKelompokjabatan()
    {
        return $this->hasOne(Kelompokjabatan::className(), ['kelompokjabatan_id' => 'kelompokjabatan_id']);
    }
    
    public function getIndexing()
    {
        return $this->hasOne(Indexing::className(), ['indexing_id' => 'indexing_id']);
    }
    
    public function extraFields()
    {
        return [
            'kelompokjabatan_m' => function($item){
                return $item->kelompokjabatan;
            },
            'indexing_m' => function($item){
                return $item->indexing;
            }
        ];
    }
}
