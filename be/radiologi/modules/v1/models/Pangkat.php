<?php

namespace app\modules\v1\models;

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
class Pangkat extends \Doco\components\DocoActiveRecord
{
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
            [['golonganpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Golonganpegawai::className(), 'targetAttribute' => ['golonganpegawai_id' => 'golonganpegawai_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pangkat_id' => 'Pangkat ID',
            'golonganpegawai_id' => 'Golongan Pegawai',
            'pangkat_urutan' => 'Urutan',
            'pangkat_nama' => 'Nama Pangkat',
            'pangkat_namalainnya' => 'Nama Lainnya',
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
    
    public function getGolonganpegawai()
    {
        return $this->hasOne(Golonganpegawai::className(), ['golonganpegawai_id' => 'golonganpegawai_id']);
    }
    
    public function extraFields()
    {
        return ['golonganpegawai_m' => function($item){
            return $item->golonganpegawai;
        }];
    }
}
