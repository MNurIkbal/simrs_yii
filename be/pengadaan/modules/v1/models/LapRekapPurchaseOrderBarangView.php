<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;

class LapRekapPurchaseOrderBarangView extends DocoExcelActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporanrekappobarang_v';
    }

    public function columnNames()
    {
        return [
            (array) new ExcelColumn('supplier_kode', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('supplier_nama', self::STRING_TYPE, 'Nama Supplier'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'No PO'),
            (array) new ExcelColumn('tgl_po', self::DATE_EXCEL, 'Tanggal PO'),
            (array) new ExcelColumn('tgl_validasi', self::DATE_EXCEL, 'Tanggal Validasi PO'),
            (array) new ExcelColumn('status_po', self::STRING_TYPE, 'Status PO'),
            (array) new ExcelColumn('tgl_batal_po', self::DATE_EXCEL, 'Tanggal Batal PO'),
            (array) new ExcelColumn('alasan_batal_po', self::STRING_TYPE, 'Alasan Batal'),
            (array) new ExcelColumn('total_harga', self::NUMBER_TYPE, 'Total Harga(Rp.)'),
        ];

    }

}