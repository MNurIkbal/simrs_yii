<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kegiatanoperasi_m".
 *
 * @property int $kegiatanoperasi_id
 * @property string $kegiatanoperasi_kode
 * @property string $kegiatanoperasi_nama
 * @property string $kegiatanoperasi_namalainnya
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
class KegiatanOperasiForm extends \yii\db\ActiveRecord
{

    // Public property
    public $kegiatanoperasi_id;
    public $kegiatanoperasi_kode;
    public $kegiatanoperasi_nama;
    public $kegiatanoperasi_namalainnya;
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
    public static function tableName()
    {
        return 'kegiatanoperasi_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kegiatanoperasi_kode', 'kegiatanoperasi_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kegiatanoperasi_kode'], 'string', 'max' => 20],
            [['kegiatanoperasi_nama', 'kegiatanoperasi_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kegiatanoperasi_id' => 'Kegiatanoperasi ID',
            'kegiatanoperasi_kode' => Yii::t('fe', 'Kode Kegiatan Operasi'),
            'kegiatanoperasi_nama' => Yii::t('fe', 'Kegiatan Operasi'),
            'kegiatanoperasi_namalainnya' => 'Kegiatanoperasi Namalainnya',
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
