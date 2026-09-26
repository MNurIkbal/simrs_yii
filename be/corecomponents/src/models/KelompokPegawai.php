<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "kelompokpegawai_m".
 *
 * @property integer $kelmenu_id
 * @property string $kelmenu_nama
 * @property string $kelmenu_key
 * @property string $kelmenu_url
 * @property string $kelmenu_icon
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
 *
 * @property MenumodulK[] $menumodulKs
 */
class KelompokPegawai extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokpegawai_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokpegawai_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','kelmenu_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokpegawai_nama', 'kelompokpegawai_namalainnya', 'kelompokpegawai_fungsi'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokpegawai_id' => 'Kelmenu ID',
            'kelompokpegawai_nama' => 'Kelompok nama pegawai',
            'kelompokpegawai_namalainnya' => 'Kelompok Nama pegawai lainnya',
            'kelompokpegawai_fungsi' => 'Kelompok pegawai fungsi',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(\Doco\models\Pegawai::classname(),['kelompokpegawai_id' => 'kelompokpegawai_id']);
    }
}
