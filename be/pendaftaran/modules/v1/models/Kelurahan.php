<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelurahan_m".
 *
 * @property integer $kelurahan_id
 * @property integer $kecamatan_id
 * @property string $kelurahan_nama
 * @property string $kelurahan_namalainnya
 * @property string $kode_pos
 * @property string $longitude
 * @property string $latitude
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
class Kelurahan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelurahan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kecamatan_id', 'kelurahan_nama'], 'required'],
            [['kecamatan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['longitude', 'latitude', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelurahan_nama', 'kelurahan_namalainnya'], 'string', 'max' => 50],
            [['kode_pos'], 'string', 'max' => 15],
            [['kecamatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kecamatan::className(), 'targetAttribute' => ['kecamatan_id' => 'kecamatan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelurahan_id' => 'Kelurahan ID',
            'kecamatan_id' => 'Kecamatan',
            'kelurahan_nama' => 'Nama Kelurahan',
            'kelurahan_namalainnya' => 'Nama Lainnya',
            'kode_pos' => 'Kode Pos',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
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
    
    public function getKecamatan()
    {
        return $this->hasOne(Kecamatan::className(), ['kecamatan_id' => 'kecamatan_id']);
    }
    
    public function extraFields()
    {
        return ['kecamatan_m' => function($item){
            return $item->kecamatan;
        }];
    }
}
