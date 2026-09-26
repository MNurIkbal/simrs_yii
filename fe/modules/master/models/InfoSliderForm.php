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
class InfoSliderForm extends \yii\base\Model
{

        public $infoslider_id;
        public $profilrs_id;
        public $tgl_mulai;
        public $tgl_selesai;
        public $judul;
        public $file_gambar;
        public $additional_data;
        public $created_date;
        public $created_by;
        public $modified_count;
        public $last_modified_date;
        public $last_modified_by;
        public $is_deleted;
        public $is_active;
        public $deleted_date;
        public $deleted_by;

    /**
     * {@inheritdoc}
     */
    // public static function tableName () {
    //     return 'infoslider_m';
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['judul', 'file_gambar'], 'required', 'on'=> 'default'],
            [['judul'], 'required', 'on'=> 'update'],
            [[ 'profilrs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [[ 'profilrs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['infoslider_id', 'profilrs_id', 'tgl_mulai', 'judul', 'file_gambar', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'is_deleted', 'is_active', 'deleted_by', 'tgl_mulai', 'tgl_selesai', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['judul'], 'string', 'max' => 255],
            [['file_gambar'], 'file', 'extensions' => 'jpg, png', 'maxSize'=> 1024 * 1024 * 2],
            // [['infoslider_id'], 'unique'],
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
            'tgl_mulai' => 'Tanggal Mulai',
            'tgl_selesai' => 'Tanggal Selesai',
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
