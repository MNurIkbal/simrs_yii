<?php

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

class LapPurchaseRequisitionOutstandingBarangView extends DocoExcelActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'laporanproutstandingbarang_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'PR No.'),
            (array) new ExcelColumn('create_date', self::DATE_EXCEL, 'Create Date'),
            (array) new ExcelColumn('approval_date', self::DATE_EXCEL, 'Approval Date'),
            (array) new ExcelColumn('item_code', self::STRING_TYPE, 'Item Code'),
            (array) new ExcelColumn('item_name', self::STRING_TYPE, 'Item Name'),
            (array) new ExcelColumn('category', self::STRING_TYPE, 'Category'),
            (array) new ExcelColumn('qty', self::STRING_TYPE, 'Qty'),
            (array) new ExcelColumn('uom', self::STRING_TYPE, 'UoM'),
            (array) new ExcelColumn('from_uom', self::STRING_TYPE, 'From UOM'),
            (array) new ExcelColumn('factor', self::STRING_TYPE, 'Factor'),
            (array) new ExcelColumn('to_uom', self::STRING_TYPE, 'To UOM'),
            (array) new ExcelColumn('remarks', self::STRING_TYPE, 'Remarks')
        ];
    }
}
