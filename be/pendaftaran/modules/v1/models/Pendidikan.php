<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pendidikan_m".
 *
 * @property integer $pendidikan_id
 * @property integer $indexing_id
 * @property integer $pendidikan_urutan
 * @property string $pendidikan_nama
 * @property string $pendidikan_namalainnya
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
class Pendidikan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendidikan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['indexing_id', 'pendidikan_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendidikan_urutan', 'pendidikan_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pendidikan_nama', 'pendidikan_namalainnya'], 'string', 'max' => 50],
            [['indexing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Indexing::className(), 'targetAttribute' => ['indexing_id' => 'indexing_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendidikan_id' => 'Pendidikan ID',
            'indexing_id' => 'Indexing ID',
            'pendidikan_urutan' => 'Pendidikan Urutan',
            'pendidikan_nama' => 'Pendidikan Nama',
            'pendidikan_namalainnya' => 'Pendidikan Namalainnya',
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
    
    public function getIndexing()
    {
        return $this->hasOne(Indexing::className(), ['indexing_id' => 'indexing_id']);
    }
    
    public function extraFields()
    {
        return ['indexing'];
    }
}
