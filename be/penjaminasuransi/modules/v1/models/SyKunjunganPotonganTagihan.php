<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sy_potongantagihan".
 *
 * @property int $potongantagihan_id
 * @property int $kunjungan_id
 * @property string $no_pendaftaran
 * @property string $tgl_transaksi
 * @property string $no_buktitransaksi
 * @property string $bagian_kode
 * @property string $bagian_layanan
 * @property string $dokter_kode
 * @property double $qty_potongan
 * @property double $tarifpot_rs
 * @property double $tarifpot_dr
 * @property double $total
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
class SyKunjunganPotonganTagihan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_potongantagihan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kunjungan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by',], 'default', 'value' => null],
            [['kunjungan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['total', 'tarifpot_dr', 'tarifpot_rs', 'qty_potongan'], 'number'],
            [['additional_data'], 'string'],
            [['kunjungan_id', 'created_date', 'last_modified_date', 'deleted_date', 'tgl_transaksi'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pendaftaran', 'dokter_kode'], 'string', 'max' => 150],
            [['no_buktitransaksi', 'bagian_kode', 'bagian_layanan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'potongantagihan_id' => 'Kunjungantagihan ID',
            'kunjungan_id' => 'Kunjungan ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_transaksi' => 'Tanggal Transaksi',
            'no_buktitransaksi' => 'No Bukti Transaksi',
            'total' => 'Total',
            'tarifpot_dr' => 'Tarif Potongan Dr',
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
            'bagian_kode' => 'Bagian Kode',
            'bagian_layanan' => 'Bagian Layanan',
            'tarifpot_rs' => 'Tarif Potongan RS',
            'qty_potongan' => 'Qty Potongan',
            'dokter_kode' => 'Kode Dokter',
        ];
    }
}
