<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopodetail_v".
 *
 * @property string $asal_transaksi
 * @property int $transaksi_id
 * @property string $tanggal_po
 * @property string $nomor
 * @property int $supplier_id
 * @property string $supplier_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $instalasi_nama
 * @property bool $is_validasi
 * @property string $status_validasi
 * @property int $status_penerimaan
 * @property string $stat_penerimaan
 * @property int $payment_term
 * @property double $total_harga_po
 * @property string $tgl_rencanaterima
 * @property int $diorder_oleh
 * @property int $peg_mengetahui_id
 * @property string $peg_mengetahui
 * @property int $peg_menyetujui_id
 * @property string $peg_menyetujui
 * @property double $sub_total
 * @property double $total_discount
 * @property int $ppn_persen
 * @property double $ppn_nilai
 * @property double $total
 * @property int $obat_barang_id
 * @property string $obat_barang_nama
 * @property int $qty
 * @property int $qty_penerimaan
 * @property string $satuan
 * @property double $harga
 * @property double $discount
 * @property double $discount_rp
 * @property double $jumlah
 * @property int $po_balance
 * @property string $is_completed
 * @property int $qty_input
 */
class InfoPoDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopodetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['asal_transaksi', 'status_validasi', 'obat_barang_nama', 'satuan', 'is_completed'], 'string'],
            [['transaksi_id', 'supplier_id', 'ruangan_id', 'status_penerimaan', 'payment_term', 'diorder_oleh', 'peg_mengetahui_id', 'peg_menyetujui_id', 'ppn_persen', 'obat_barang_id', 'qty', 'qty_penerimaan', 'po_balance', 'qty_input'], 'default', 'value' => null],
            [['transaksi_id', 'supplier_id', 'ruangan_id', 'status_penerimaan', 'payment_term', 'diorder_oleh', 'peg_mengetahui_id', 'peg_menyetujui_id', 'ppn_persen', 'obat_barang_id', 'qty', 'qty_penerimaan', 'po_balance', 'qty_input'], 'integer'],
            [['tanggal_po', 'tgl_rencanaterima'], 'safe'],
            [['is_validasi'], 'boolean'],
            [['total_harga_po', 'sub_total', 'total_discount', 'ppn_nilai', 'total', 'harga', 'discount', 'discount_rp', 'jumlah'], 'number'],
            [['nomor'], 'string', 'max' => 255],
            [['supplier_nama'], 'string', 'max' => 100],
            [['ruangan_nama', 'instalasi_nama', 'peg_mengetahui', 'peg_menyetujui'], 'string', 'max' => 50],
            [['stat_penerimaan'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asal_transaksi' => 'Asal Transaksi',
            'transaksi_id' => 'Transaksi ID',
            'tanggal_po' => 'Tanggal Po',
            'nomor' => 'Nomor',
            'supplier_id' => 'Supplier ID',
            'supplier_nama' => 'Supplier Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_nama' => 'Instalasi Nama',
            'is_validasi' => 'Is Validasi',
            'status_validasi' => 'Status Validasi',
            'status_penerimaan' => 'Status Penerimaan',
            'stat_penerimaan' => 'Stat Penerimaan',
            'payment_term' => 'Payment Term',
            'total_harga_po' => 'Total Harga Po',
            'tgl_rencanaterima' => 'Tgl Rencanaterima',
            'diorder_oleh' => 'Diorder Oleh',
            'peg_mengetahui_id' => 'Peg Mengetahui ID',
            'peg_mengetahui' => 'Peg Mengetahui',
            'peg_menyetujui_id' => 'Peg Menyetujui ID',
            'peg_menyetujui' => 'Peg Menyetujui',
            'sub_total' => 'Sub Total',
            'total_discount' => 'Total Discount',
            'ppn_persen' => 'Ppn Persen',
            'ppn_nilai' => 'Ppn Nilai',
            'total' => 'Total',
            'obat_barang_id' => 'Obat Barang ID',
            'obat_barang_nama' => 'Obat Barang Nama',
            'qty' => 'Qty',
            'qty_penerimaan' => 'Qty Penerimaan',
            'satuan' => 'Satuan',
            'harga' => 'Harga',
            'discount' => 'Discount',
            'discount_rp' => 'Discount Rp',
            'jumlah' => 'Jumlah',
            'po_balance' => 'Po Balance',
            'is_completed' => 'Is Completed',
            'qty_input' => 'Qty Input',
        ];
    }
}
