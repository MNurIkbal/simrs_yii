<?php

namespace Integrasi\Service\Akunting\Models;

use Yii;

/**
 * This is the model class for table "syncakuntansi_r".
 *
 * @property int $syncakuntansi_id
 * @property int $jenisobatalkes_id
 * @property int $komponentarif_id
 * @property int $tindakankomponen_id
 * @property int $obatalkespasien_id
 * @property bool $is_sync
 * @property string $additional_data
 * @property int $pemakaianobatdetail_id
 * @property int $adjusmenobatkeluar_id
 * @property int $penjualanresep_id
 * @property int $pembayaranpelayanan_id
 * @property int $penerimaanbarang_id
 * @property int $penerimaanobat_id
 * @property int $adjusmenobat_id
 * @property int $adjusmenbarang_id
 * @property int $adjusmenbarangkeluar_id
 * @property int $adjusmenobatkeluar_id
 * @property int $returpenerimaanobat_id
 * @property int $returpenerimaanbarang_id
 * @property int $stokopname_id
 * @property int $stokopnamebarang_id
 * @property int $pengajuanklaim_id
 */
class SyncAkuntansi extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syncakuntansi_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_id', 'komponentarif_id', 'tindakankomponen_id', 'obatalkespasien_id', 'pemakaianobatdetail_id', 'adjusmenobatkeluar_id', 'penjualanresep_id', 'pembayaranpelayanan_id', 'penerimaanobat_id', 'penerimaanbarang_id', 'adjusmenobat_id', 'adjusmenbarang_id', 'adjusmenobatkeluar_id', 'adjusmenbarangkeluar_id', 'returpenerimaanobat_id', 'returpenerimaanbarang_id', 'stokopname_id', 'stokopnamebarang_id', 'pengajuanklaim_id'], 'integer'],
            [['is_sync'], 'boolean'],
            [['additional_data'], 'string'],
            [['jenisobatalkes_id', 'komponentarif_id', 'tindakankomponen_id', 'obatalkespasien_id', 'pemakaianobatdetail_id', 'adjusmenobatkeluar_id', 'penjualanresep_id', 'pembayaranpelayanan_id', 'penerimaanobat_id', 'penerimaanbarang_id', 'adjusmenobat_id', 'adjusmenbarang_id', 'adjusmenobatkeluar_id', 'adjusmenbarangkeluar_id', 'returpenerimaanobat_id', 'returpenerimaanbarang_id', 'stokopname_id', 'stokopnamebarang_id', 'pengajuanklaim_id'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'syncakuntansi_id' => 'Syncakuntansi ID',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'komponentarif_id' => 'Komponentarif ID',
            'tindakankomponen_id' => 'Tindakankomponen ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'is_sync' => 'Is Sync',
            'additional_data' => 'Additional Data',
            'pemakaianobatdetail_id' => 'Pemakaianobatdetail ID',
            'adjusmenobatkeluar_id' => 'Adjusmenobatkeluar ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'pembayaranpelayanan_id' => 'Pembayaran ID',
            'penerimaanobat_id' => 'Penerimaan Obat ID',
            'penerimaanbarang_id' => 'Penerimaan Barang ID',
            'adjusmenobat_id' => 'Adjusment Obat Masuk ID',
            'adjusmenbarang_id' => 'Adjusment Barang Masuk ID',
            'adjusmenobatkeluar_id' => 'Adjusment Obat Keluar',
            'adjusmenbarangkeluar_id' => 'Adjustment Barang Keluar',
            'returpenerimaanobat_id' => 'Retur Obat ID',
            'returpenerimaanbarang_id' => 'Retur Barang ID',
            'stokopname_id' => 'Stok Opname Obat ID',
            'stokopnamebarang_id' => 'Stok Opname Barang ID',
            'pengajuanklaim_id' => 'Klaim ID'
        ];
    }
}
