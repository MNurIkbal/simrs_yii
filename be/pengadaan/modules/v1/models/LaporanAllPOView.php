<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanAllPOView extends DocoExcelActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporanallpo_v';
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
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'PR No.'),
            (array) new ExcelColumn('tanggal_verifikasi_pr', self::DATE_EXCEL, 'PR Approval Date'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'PO No.'),
            (array) new ExcelColumn('tanggal_po', self::DATE_EXCEL, 'PO Create Date'),
            (array) new ExcelColumn('tgl_verifikasi_po', self::DATE_EXCEL, 'PO Approval Date'),
            (array) new ExcelColumn('supplier_code', self::STRING_TYPE, 'Supplier Code'),
            (array) new ExcelColumn('supplier_name', self::STRING_TYPE, 'Supplier Name'),
            (array) new ExcelColumn('manufacturer', self::STRING_TYPE, 'Manufacturer'),
            (array) new ExcelColumn('item_code', self::STRING_TYPE, 'Item Code'),
            (array) new ExcelColumn('item_name', self::STRING_TYPE, 'Item Name'),
            (array) new ExcelColumn('qty_po', self::STRING_TYPE, 'Qty'),
            (array) new ExcelColumn('po_balance', self::STRING_TYPE, 'Qty GRN'),
            (array) new ExcelColumn('qty_outstanding', self::STRING_TYPE, 'Qty Outstanding'),
            (array) new ExcelColumn('uom', self::STRING_TYPE, 'UOM'),
            (array) new ExcelColumn('from_uom', self::STRING_TYPE, 'From UOM'),
            (array) new ExcelColumn('factor', self::STRING_TYPE, 'Factor'),
            (array) new ExcelColumn('to_uom', self::STRING_TYPE, 'To UOM'),
            (array) new ExcelColumn('price', self::STRING_TYPE, 'Price'),
            (array) new ExcelColumn('deduction_percent', self::STRING_TYPE, 'Discount (%)'),
            (array) new ExcelColumn('deduction_rupiah', self::STRING_TYPE, 'Discount (Rp.)'),
            (array) new ExcelColumn('addition_percent', self::STRING_TYPE, 'Tax (%)'),
            (array) new ExcelColumn('gross_amount', self::STRING_TYPE, 'Gross Amount'),
            (array) new ExcelColumn('nett_amount', self::STRING_TYPE, 'Nett Amount'),
            (array) new ExcelColumn('remarks', self::STRING_TYPE, 'PO Remarks'),
            (array) new ExcelColumn('catatan_1', self::STRING_TYPE, 'Catatan Internal'),
            (array) new ExcelColumn('catatan_2', self::STRING_TYPE, 'Catatan Eksternal'),
            (array) new ExcelColumn('status_po', self::STRING_TYPE, 'Status PO'),
            (array) new ExcelColumn('reject_date', self::DATE_EXCEL, 'Reject Date'),
            (array) new ExcelColumn('reject_remarks', self::STRING_TYPE, 'Reject Remarks'),
            (array) new ExcelColumn('tanggal_penerimaan', self::DATE_EXCEL, 'Tanggal Penerimaan')
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