<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokpemeriksaanlab_m".
 *
 * @property int $kelompokpemeriksaanlab_id
 * @property string $kode_kelompok
 * @property string $nama_kelompok
 * @property string $keterangan_kelompok
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
 */
class KelompokPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokpemeriksaanlab_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode_kelompok', 'nama_kelompok'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kode_kelompok'], 'string', 'max' => 25],
            [['nama_kelompok', 'keterangan_kelompok'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
            'kode_kelompok' => 'Kode Kelompok',
            'nama_kelompok' => 'Nama Kelompok',
            'keterangan_kelompok' => 'Keterangan Kelompok',
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
