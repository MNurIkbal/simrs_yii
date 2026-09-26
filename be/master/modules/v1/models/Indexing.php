<?php

namespace app\modules\v1\models;

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
class Indexing extends \Doco\components\DocoActiveRecord
{
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
            [['kelompokremunerasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokRemunerasi::className(), 'targetAttribute' => ['kelompokremunerasi_id' => 'kelompokremunerasi_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'indexing_id' => 'Indexing ID',
            'kelompokremunerasi_id' => 'Kelompok Remunerasi',
            'indexing_urutan' => 'Urutan Indexing',
            'indexing_nama' => 'Nama Indexing',
            'indexing_singk' => 'Singkatan',
            'indexing_nilai' => 'Nilai Indexing',
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
    
    public function getKelompokRemunerasi()
    {
        return $this->hasOne(KelompokRemunerasi::className(), ['kelompokremunerasi_id' => 'kelompokremunerasi_id']);
    }

    public function getPendidikan()
    {
        return $this->hasOne(Pendidikan::className(), ['indexing_id' => 'indexing_id']);
    }
    
    public function extraFields()
    {
        return ['kelompokremunerasi_m' => function($item){
            return $item->kelompokRemunerasi;
        }];
    }
}
