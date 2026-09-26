<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pembayaranalokasi_t".
 *
 * @property int $pembayaranalokasi_id
 * @property string $tgl_pembayaranalokasi
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $pengajuanklaim_id
 * @property int $terimabayarklaim_id
 * @property double $jumlah_pembayaran
 * @property double $total_pengajuan
 * @property double $total_terbayar
 * @property double $sisa_piutang
 * @property string $catatan
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
class PembayaranAlokasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pembayaranalokasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pembayaranalokasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['tgl_pembayaranalokasi'], 'datetime',  'format' => 'php:Y-m-d H:i:s'],
            [['carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'pengajuanklaim_id', 'terimabayarklaim_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'pengajuanklaim_id', 'terimabayarklaim_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah_pembayaran', 'total_pengajuan', 'total_terbayar', 'sisa_piutang'], 'number'],
            [['catatan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pembayaranalokasi_id' => 'Pembayaranalokasi ID',
            'tgl_pembayaranalokasi' => 'Tgl Pembayaranalokasi',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'pengajuanklaim_id' => 'Pengajuanklaim ID',
            'terimabayarklaim_id' => 'Terimabayarklaim ID',
            'jumlah_pembayaran' => 'Jumlah Pembayaran',
            'total_pengajuan' => 'Total Pengajuan',
            'total_terbayar' => 'Total Terbayar',
            'sisa_piutang' => 'Sisa Piutang',
            'catatan' => 'Catatan',
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
