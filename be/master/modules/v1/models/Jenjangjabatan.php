<?php

namespace app\modules\v1\models;

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
class JenjangJabatan extends \Doco\components\DocoActiveRecord
{
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
            [['indexing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Indexing::className(), 'targetAttribute' => ['indexing_id' => 'indexing_id']],
            [['jenisjabatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisJabatan::className(), 'targetAttribute' => ['jenisjabatan_id' => 'jenisjabatan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenjangjabatan_id' => 'Jenjang Jabatan ID',
            'indexing_id' => 'Indexing',
            'jenisjabatan_id' => 'Jenis Jabatan',
            'jenjangjabatan_nama' => 'Nama Jenjang Jabatan',
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
    
    public function getJenisJabatan()
    {
        return $this->hasOne(JenisJabatan::className(), ['jenisjabatan_id' => 'jenisjabatan_id']);
    }
    
    public function getIndexing()
    {
        return $this->hasOne(Indexing::className(), ['indexing_id' => 'indexing_id']);
    }
    
    public function extraFields()
    {
        return [
            'jenisjabatan_m' => function($item){
                return $item->jenisJabatan;
            },
            'indexing_m' => function($item){
                return $item->indexing;
            }
        ];
    }
}
