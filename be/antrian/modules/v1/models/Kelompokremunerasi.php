<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokremunerasi_m".
 *
 * @property integer $kelompokremunerasi_id
 * @property integer $kelompokremunerasi_urutan
 * @property string $kelompokremunerasi_kode
 * @property string $kelompokremunerasi_nama
 * @property string $kelompokremunerasi_desc
 * @property string $kelompokremunerasi_singkatan
 * @property integer $kelompokremunerasi_rate
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
class Kelompokremunerasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokremunerasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokremunerasi_urutan', 'kelompokremunerasi_kode', 'kelompokremunerasi_nama', 'kelompokremunerasi_desc', 'kelompokremunerasi_singkatan', 'kelompokremunerasi_rate'], 'required'],
            [['kelompokremunerasi_urutan', 'kelompokremunerasi_rate', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kelompokremunerasi_kode'], 'string', 'max' => 50],
            [['kelompokremunerasi_nama'], 'string', 'max' => 100],
            [['kelompokremunerasi_desc'], 'string', 'max' => 200],
            [['kelompokremunerasi_singkatan'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokremunerasi_id' => 'ID',
            'kelompokremunerasi_urutan' => 'Urutan',
            'kelompokremunerasi_kode' => 'Kode Kelompok',
            'kelompokremunerasi_nama' => 'Nama Kelompok',
            'kelompokremunerasi_desc' => 'Deskripsi',
            'kelompokremunerasi_singkatan' => 'Singkatan',
            'kelompokremunerasi_rate' => 'Rate',
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
}
