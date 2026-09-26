<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "laporanrekappenerimaanobat_v".
 */

class LaporanRekapPenerimaanObatView extends DocoExcelActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'laporanrekappenerimaanobat_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('supplier_kode', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('supplier_nama', self::STRING_TYPE, 'Nama Supplier'),
            (array) new ExcelColumn('tgl_penerimaan', self::DATE_EXCEL, 'Tanggal Penerimaan'),
            (array) new ExcelColumn('no_penerimaan', self::STRING_TYPE, 'Nomor Penerimaan'),
            (array) new ExcelColumn('diterima_oleh', self::STRING_TYPE, 'Diterima Oleh'),
            (array) new ExcelColumn('payterm_nama', self::STRING_TYPE, 'Payment Term'),
            (array) new ExcelColumn('status_penerimaan', self::STRING_TYPE, 'Status Penerimaan'),
            (array) new ExcelColumn('tgl_po', self::DATE_EXCEL, 'Tanggal PO'),
            (array) new ExcelColumn('tgl_validasi_po', self::DATE_EXCEL, 'Tanggal Validasi PO'),
            (array) new ExcelColumn('nomor_po', self::STRING_TYPE, 'No.PO'),
            (array) new ExcelColumn('no_suratjalan', self::STRING_TYPE, 'No. Surat Jalan'),
            (array) new ExcelColumn('no_faktur', self::STRING_TYPE, 'No. Faktur'),
            (array) new ExcelColumn('total', self::NUMBER_TYPE, 'Total Harga'),
        ];
    }
}
