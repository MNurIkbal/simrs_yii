<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopo_v".
 *
 * @property string $asal_transaksi
 * @property int $transaksi_id
 * @property string $tanggal_po
 * @property string $nomor
 * @property int $supplier_id
 * @property string $supplier_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property bool $is_validasi
 * @property string $status_validasi
 * @property int $status_penerimaan
 * @property string $stat_penerimaan
 * @property int $payment_term
 * @property double $total_harga_po
 */
class InfoPoView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopo_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['asal_transaksi', 'status_validasi'], 'string'],
            [['transaksi_id', 'supplier_id', 'ruangan_id', 'status_penerimaan', 'payment_term'], 'default', 'value' => null],
            [['transaksi_id', 'supplier_id', 'ruangan_id', 'status_penerimaan', 'payment_term'], 'integer'],
            [['tanggal_po'], 'safe'],
            [['is_validasi'], 'boolean'],
            [['total_harga_po'], 'number'],
            [['nomor'], 'string', 'max' => 255],
            [['supplier_nama'], 'string', 'max' => 100],
            [['ruangan_nama'], 'string', 'max' => 50],
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
            'is_validasi' => 'Is Validasi',
            'status_validasi' => 'Status Validasi',
            'status_penerimaan' => 'Status Penerimaan',
            'stat_penerimaan' => 'Stat Penerimaan',
            'payment_term' => 'Payment Term',
            'total_harga_po' => 'Total Harga Po',
        ];
    }

    public function columns()
    {
        return [
            'asal_transaksi',
            'type_po',
            'transaksi_id',
            'tanggal_buat_po',
            'tanggal_po',
            'tgl_rekomendasi',
            'no_transaksi',
            'supplier_id',
            'supplier_nama',
            'ruangan_id',
            'ruangan_nama',
            'instalasi_nama',
            'is_validasi',
            'status_validasi',
            'status_penerimaan',
            'stat_penerimaan',
            'payment_term',
            'total_harga_po',
            'tgl_rencanaterima',
            'peg_mengetahui_id',
            'peg_mengetahui',
            'peg_menyetujui_id',
            'peg_menyetujui',
            'sub_total',
            'total_discount',
            'ppn_persen',
            'ppn_nilai',
            'total',
            'lookup_id',
            'payterm_id',
            'diorder_oleh',
            'diorder_oleh_nama',
            'pajak_id',
            'catatan1',
            'catatan2',
            'is_closing',
            'instalasi_id',
            'peg_mengubah',
            'tgl_perubahan',
            'pegawai_validasi',
            'nomor',
            'tgl_penerimaan',
            'qty_po',
            'status_po_id',
            'status_po_nama',
            'tgl_batal_po',
            'catatan_batal_po',
            'tgl_tercetak',
            'po_cito', 
            'po_admin',
            'po_consigment'
        ];
    }
}
