<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use yii\helpers\ArrayHelper;
use Doco\components\DocoExcelActiveRecord;

class LaporanPenerimaanBarangView extends DocoExcelActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporanpenerimaanbarang_v'; //Sementara
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('supplier_kode', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('supplier_nama', self::STRING_TYPE, 'Supplier'),
            (array) new ExcelColumn('payterm_nama', self::STRING_TYPE, 'Payment Term'),
            (array) new ExcelColumn('tgl_penerimaan', self::DATE_EXCEL, 'Tanggal Penerimaan'),
            (array) new ExcelColumn('no_penerimaan', self::STRING_TYPE, 'Nomor Penerimaan'),
            (array) new ExcelColumn('diterima_oleh', self::STRING_TYPE, 'Diterima Oleh'),
            (array) new ExcelColumn('status_penerimaan', self::STRING_TYPE, 'Status Penerimaan'),
            (array) new ExcelColumn('tgl_po', self::DATE_EXCEL, 'Tanggal Dibuat PO'),
            (array) new ExcelColumn('tgl_validasi_po', self::DATE_EXCEL, 'Tanggal Validasi PO'),
            (array) new ExcelColumn('nomor_po', self::STRING_TYPE, 'Nomor PO'),
            (array) new ExcelColumn('kode_item', self::STRING_TYPE, 'Kode Barang'),
            (array) new ExcelColumn('barang_nama', self::STRING_TYPE, 'Nama Barang'),
            (array) new ExcelColumn('qty_po', self::STRING_TYPE, 'Qty PO'),
            (array) new ExcelColumn('satuan_po', self::STRING_TYPE, 'Satuan PO'),
            (array) new ExcelColumn('qty_diterima', self::STRING_TYPE, 'Qty Diterima'),
            (array) new ExcelColumn('satuan_terima', self::STRING_TYPE, 'Satuan Diterima'),
            (array) new ExcelColumn('po_balance', self::STRING_TYPE, 'PO Balance'),
            (array) new ExcelColumn('satuan_balance', self::STRING_TYPE, 'Satuan PO Balance'),
            (array) new ExcelColumn('nilai_konversi', self::STRING_TYPE, 'Nilai Konversi'),
            (array) new ExcelColumn('qty_konversi', self::STRING_TYPE, 'Qty Konversi'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan Kecil'),
            (array) new ExcelColumn('barang_harganetto', self::STRING_TYPE, 'Harga Netto (Rp.)'),
            (array) new ExcelColumn('harga', self::STRING_TYPE, 'Harga (Rp.)'),
            (array) new ExcelColumn('discount', self::STRING_TYPE, 'Disc. (%)'),
            (array) new ExcelColumn('ppn_persen', self::STRING_TYPE, 'PPN (%)'),
            (array) new ExcelColumn('sub_total', self::STRING_TYPE, 'Subtotal (Rp.)'),
            (array) new ExcelColumn('total', self::STRING_TYPE, 'Total (Rp.)'),
            (array) new ExcelColumn('catatan_po', self::STRING_TYPE, 'Catatan PO'),
            (array) new ExcelColumn('tgl_pr', self::DATE_EXCEL, 'Tanggal Dibuat PR'),
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'Nomor PR'),
            (array) new ExcelColumn('no_batch', self::STRING_TYPE, 'Nomor Batch'),
            (array) new ExcelColumn('tgl_kadaluarsa', self::DATE_EXCEL, 'Tanggal Kadaluarsa'),
            (array) new ExcelColumn('no_suratjalan', self::STRING_TYPE, 'Nomor Surat Jalan'),
            (array) new ExcelColumn('no_faktur', self::STRING_TYPE, 'Nomor Faktur')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
