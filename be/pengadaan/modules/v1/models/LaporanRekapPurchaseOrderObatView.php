<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

class LaporanRekapPurchaseOrderObatView extends DocoExcelActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporanrekappoobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function columnNames() {
        return [
            (array) new ExcelColumn('supplier_kode', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('supplier_nama', self::STRING_TYPE, 'Nama Supplier'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'No. PO'),
            (array) new ExcelColumn('tgl_po', self::DATE_EXCEL, 'Tanggal PO'),
            (array) new ExcelColumn('tgl_validasi', self::DATE_EXCEL, 'Tanggal Validasi'),
            (array) new ExcelColumn('status_po', self::STRING_TYPE, 'Status PO'),
            (array) new ExcelColumn('tgl_batal_po', self::DATE_EXCEL, 'Tanggal Batal'),
            (array) new ExcelColumn('alasan_batal_po', self::STRING_TYPE, 'Alasan Batal'),
            (array) new ExcelColumn('total_harga', self::NUMBER_TYPE, 'Total Harga')
        ];
    }
}
