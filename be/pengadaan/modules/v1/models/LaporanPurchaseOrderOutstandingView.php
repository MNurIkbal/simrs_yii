<?php

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

/**
 * This is the model class for table "inforeturresep_v".
 *
 * @property int $returresep_id
 * @property string $tgl_retur
 * @property string $no_returresep
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property int $penjualanresep_id
 * @property string $noresep
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 */
class LaporanPurchaseOrderOutstandingView extends DocoExcelActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'laporanpooutstanding_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('tgl_pr', self::DATE_EXCEL, 'Tanggal PR'),
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'Nomor PR'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'Nomor PO'),
            (array) new ExcelColumn('tgl_po_dibuat', self::DATE_EXCEL, 'Tanggal PO'),
            (array) new ExcelColumn('tgl_validasi', self::DATE_EXCEL, 'Tanggal Validasi PO'),
            (array) new ExcelColumn('manufaktur_nama', self::STRING_TYPE, 'Nama Manufaktur'),
            (array) new ExcelColumn('supplier_kode', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('supplier_nama', self::STRING_TYPE, 'Nama Supplier'),
            (array) new ExcelColumn('jenisobatalkes_nama', self::STRING_TYPE, 'Jenis Obat'),
            (array) new ExcelColumn('kode_item', self::STRING_TYPE, 'Kode Item'),
            (array) new ExcelColumn('nama_item', self::STRING_TYPE, 'Nama Item'),
            (array) new ExcelColumn('qty', self::STRING_TYPE, 'Qty PO'),
            (array) new ExcelColumn('po_balance', self::STRING_TYPE, 'PO Balance'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('uom', self::STRING_TYPE, 'UoM'),
            (array) new ExcelColumn('harga', self::STRING_TYPE, 'Harga (Rp.)'),
            (array) new ExcelColumn('discount', self::STRING_TYPE, 'Diskon (%)'),
            (array) new ExcelColumn('ppn_persen', self::STRING_TYPE, 'PPn (%)'),
            (array) new ExcelColumn('sub_total', self::STRING_TYPE, 'Sub Total (Rp.)'),
            (array) new ExcelColumn('total', self::STRING_TYPE, 'Total setelah PPn (Rp.)'),
            (array) new ExcelColumn('catatan1', self::STRING_TYPE, 'Catatan 1'),
            (array) new ExcelColumn('catatan2', self::STRING_TYPE, 'Catatan 2')
        ];
    }
}
