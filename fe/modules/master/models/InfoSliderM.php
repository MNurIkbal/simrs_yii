<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "infoslider_m".
 *
 * @property int $infoslider_id
 * @property int $profilrs_id
 * @property string $tgl_mulai
 * @property string $tgl_selesai
 * @property string $judul
 * @property string $file_gambar
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
class InfoSliderM extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoslider_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['infoslider_id', 'judul', 'file_gambar'], 'required'],
            [['infoslider_id', 'profilrs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['infoslider_id', 'profilrs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_mulai', 'tgl_selesai', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['file_gambar', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['judul'], 'string', 'max' => 255],
            [['infoslider_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'infoslider_id' => 'Infoslider ID',
            'profilrs_id' => 'Profilrs ID',
            'tgl_mulai' => 'Tgl Mulai',
            'tgl_selesai' => 'Tgl Selesai',
            'judul' => 'Judul',
            'file_gambar' => 'File Gambar',
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
