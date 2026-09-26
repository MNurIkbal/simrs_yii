<?php

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

/**
 * This is the model class for table "laporanpooutstandingbarang_v".
 *
 */

class LaporanPurchaseOrderOutstandingBarangView extends DocoExcelActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'laporanpooutstandingbarang_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'PR No.'),
            (array) new ExcelColumn('tanggal_verifikasi_pr', self::DATE_EXCEL, 'PR Approval Date'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'PO No.'),
            (array) new ExcelColumn('tanggal_po', self::DATE_EXCEL, 'PO Create Date'),
            (array) new ExcelColumn('tanggal_verifikasi_po', self::DATE_EXCEL, 'PO Approval Date'),
            (array) new ExcelColumn('supplier_kode', self::STRING_TYPE, 'Supplier Code'),
            (array) new ExcelColumn('supplier_nama', self::STRING_TYPE, 'Supplier'),
            (array) new ExcelColumn('manufacturer', self::STRING_TYPE, 'Manufacturer'),
            (array) new ExcelColumn('item_code', self::STRING_TYPE, 'Item Code'),
            (array) new ExcelColumn('item_name', self::STRING_TYPE, 'Item Name'),
            (array) new ExcelColumn('qty_po', self::STRING_TYPE, 'Qty'),
            (array) new ExcelColumn('uom', self::STRING_TYPE, 'UOM'),
            (array) new ExcelColumn('from_uom', self::STRING_TYPE, 'FROM UOM'),
            (array) new ExcelColumn('factor', self::STRING_TYPE, 'Factor'),
            (array) new ExcelColumn('to_uom', self::STRING_TYPE, 'TO UOM'),
            (array) new ExcelColumn('price', self::STRING_TYPE, 'Price'),
            (array) new ExcelColumn('deduction_percent', self::STRING_TYPE, 'Ded %'),
            (array) new ExcelColumn('addition_percent', self::STRING_TYPE, 'Add %'),
            (array) new ExcelColumn('gross_amount', self::STRING_TYPE, 'Gross Amount'),
            (array) new ExcelColumn('nett_amount', self::STRING_TYPE, 'Nett Amount Incl PPN'),
            (array) new ExcelColumn('remarks', self::STRING_TYPE, 'PO REMARKS')
        ];
    }
}
