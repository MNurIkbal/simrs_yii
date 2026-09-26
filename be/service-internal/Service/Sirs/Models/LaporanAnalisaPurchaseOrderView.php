<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

class LaporanAnalisaPurchaseOrderView extends DocoExcelActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'laporananalisapo_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('kode_obat', self::STRING_TYPE, 'Kode Obat'),
            (array) new ExcelColumn('nama_obat', self::STRING_TYPE, 'Nama Obat'),
            (array) new ExcelColumn('manufaktur', self::STRING_TYPE, 'Manufaktur'),
            (array) new ExcelColumn('jenis_obat', self::STRING_TYPE, 'Jenis Obat'),
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'Nomor PR'),
            (array) new ExcelColumn('created_date_pr', self::DATE_EXCEL, 'Tanggal PR'), // tadinya tgl_pr
            (array) new ExcelColumn('tgl_approve', self::DATE_EXCEL, 'Tanggal Approve PR'),
            (array) new ExcelColumn('qty_pr', self::STRING_TYPE, 'Qty PR'),
            (array) new ExcelColumn('satuan_pr', self::STRING_TYPE, 'UoM PR'),
            (array) new ExcelColumn('catatan', self::STRING_TYPE, 'Catatan'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'Nomor PO'),
            (array) new ExcelColumn('po_cito', self::STRING_TYPE, 'Cito'),
            (array) new ExcelColumn('po_admin', self::STRING_TYPE, 'Admin'),
            (array) new ExcelColumn('po_consigment', self::STRING_TYPE, 'Consignment'),
            (array) new ExcelColumn('tgl_po', self::DATE_EXCEL, 'Tanggal PO'),
            (array) new ExcelColumn('tgl_po_validasi', self::DATE_EXCEL, 'Tanggal Validasi PO'),
            (array) new ExcelColumn('qty_po', self::STRING_TYPE, 'Qty PO'),
            (array) new ExcelColumn('satuan_po', self::STRING_TYPE, 'UoM PO'),
            (array) new ExcelColumn('harga', self::STRING_TYPE, 'Harga (Rp.)'),
            (array) new ExcelColumn('disc_persen', self::STRING_TYPE, 'Diskon (%)'),
            (array) new ExcelColumn('ppn_persen', self::STRING_TYPE, 'PPn (%)'),
            (array) new ExcelColumn('sub_total', self::STRING_TYPE, 'Sub Total (Rp.)'),
            (array) new ExcelColumn('total', self::STRING_TYPE, 'Total setelah PPn (Rp.)'),
            (array) new ExcelColumn('status_po_kondisi', self::STRING_TYPE, 'Status PO'),
            (array) new ExcelColumn('tgl_po_batal', self::DATE_EXCEL, 'Tanggal Batal PO'),
            (array) new ExcelColumn('catatan_batal', self::STRING_TYPE, 'Catatan Batal PO'),
            (array) new ExcelColumn('kode_supplier', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('nama_supplier', self::STRING_TYPE, 'Nama Supplier'),
            (array) new ExcelColumn('tgl_penerimaan', self::DATE_EXCEL, 'Tanggal Penerimaan'),
            (array) new ExcelColumn('qty_penerimaan', self::STRING_TYPE, 'Qty Penerimaan'),
            (array) new ExcelColumn('penerimaan', self::STRING_TYPE, 'UoM Penerimaan'),
            (array) new ExcelColumn('sisa_penerimaan', self::STRING_TYPE, 'Sisa Penerimaan'),
            (array) new ExcelColumn('penerimaan', self::STRING_TYPE, 'UoM Sisa Penerimaan'),
            (array) new ExcelColumn('pr_to_po', self::STRING_TYPE, 'PR diapprove ke PO dibuat (hari)'),
            (array) new ExcelColumn('pr_to_povalidasi', self::STRING_TYPE, 'PR diapprove ke PO divalidasi (hari)'),
            (array) new ExcelColumn('po_to_povalidasi', self::STRING_TYPE, 'PO di buat ke PO di validasi (hari)'),
            (array) new ExcelColumn('pr_to_penerimaan', self::STRING_TYPE, 'PR diapprove ke penerimaan (hari)'),
            (array) new ExcelColumn('povalidasi_to_penerimaan', self::STRING_TYPE, 'PO di validasi ke Penerimaan (hari)')
        ];
    }
}
