<?php

namespace Doco\models\Bedah;

use Yii;

/**
 * This is the model class for table "penggunaancairan_t".
 *
 * @property int $penggunaancairan_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property string $kegiatan
 * @property string $cairan_masuk
 * @property string $cairan_keluar
 * @property string $keterangan
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
class PenggunaanCairan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penggunaancairan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['keterangan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kegiatan', 'cairan_masuk', 'cairan_keluar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penggunaancairan_id' => 'Penggunaancairan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'kegiatan' => 'Kegiatan',
            'cairan_masuk' => 'Cairan Masuk',
            'cairan_keluar' => 'Cairan Keluar',
            'keterangan' => 'Keterangan',
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
