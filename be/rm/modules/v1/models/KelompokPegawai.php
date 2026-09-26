<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokpegawai_m".
 *
 * @property int $kelompokpegawai_id
 * @property string $kelompokpegawai_nama
 * @property string $kelompokpegawai_namalainnya
 * @property string $kelompokpegawai_fungsi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property PegawaiM[] $pegawaiMs
 * @property PendidikankualifikasiM[] $pendidikankualifikasiMs
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
            [['kelompokpegawai_nama'], 'required'],
            [['kelompokpegawai_fungsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokpegawai_nama', 'kelompokpegawai_namalainnya'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'kelompokpegawai_nama' => 'Kelompokpegawai Nama',
            'kelompokpegawai_namalainnya' => 'Kelompokpegawai Namalainnya',
            'kelompokpegawai_fungsi' => 'Kelompokpegawai Fungsi',
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
        return $this->hasMany(Pegawai::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPendidikankualifikasiMs()
    // {
    //     return $this->hasMany(PendidikankualifikasiM::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    // }
     
    public function listKelompokPegawai()
    {
        $query = self::find()->where([
            'is_active' => 1
        ])
        ->all();

        return $query;
    }
}
