<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienbatalpulang_t".
 *
 * @property int $pasienbatalpulang_id
 * @property int $pasienpulang_id
 * @property int $pasienadmisi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $tgl_pembatalan
 * @property string $keterangan_batal
 * @property string $alasan_pembatalan
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
 */
class PasienBatalPulang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienbatalpulang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienpulang_id', 'tgl_pembatalan', 'alasan_pembatalan'], 'required'],
            [['pasienpulang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienpulang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pembatalan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data', 'alasan_pembatalan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienbatalpulang_id' => 'Pasien Batal Pulang ID',
            'pasienpulang_id' => 'Pasien Pulang ID',
            'tgl_pembatalan' => 'Tgl Pembatalan',
            'alasan_pembatalan' => 'Alasan Pembatalan',
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
