<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use Integrasi\Classes\ExcelColumn;
use Integrasi\Components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanSumStokOpnameView extends DocoExcelActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporansumso_v';
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
            (array) new ExcelColumn('no', self::STRING_TYPE, 'No'),
            (array) new ExcelColumn('store', self::STRING_TYPE, 'Store'),
            (array) new ExcelColumn('noformulir', self::STRING_TYPE, 'No. Form SO'),
            (array) new ExcelColumn('tglformulir', self::DATE_EXCEL, 'Tanggal Formulir SO'),
            (array) new ExcelColumn('tgl_validasi', self::DATE_EXCEL, 'Tanggal Validasi SO'),
            (array) new ExcelColumn('validasi_oleh', self::STRING_TYPE, 'Di Validasi Oleh'),
            (array) new ExcelColumn('jenis_obat', self::STRING_TYPE, 'Jenis Obat Alkes'),
            (array) new ExcelColumn('kode_obat', self::STRING_TYPE, 'Kode Obat Alkes'),
            (array) new ExcelColumn('nama_obat', self::STRING_TYPE, 'Nama Obat Alkes'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('konversi', self::STRING_TYPE, 'Konversi'),
            (array) new ExcelColumn('weighted_average', self::STRING_TYPE, 'Weighted Average'),
            (array) new ExcelColumn('system_stock_qty', self::STRING_TYPE, 'System Stock Qty'),
            (array) new ExcelColumn('physical_stock_qty', self::STRING_TYPE, 'Physical Stock Qty'),
            (array) new ExcelColumn('variance_qty', self::STRING_TYPE, 'Variance Qty'),
            (array) new ExcelColumn('opening_total_batch_cost', self::STRING_TYPE, 'Opening Total Batch Cost'),
            (array) new ExcelColumn('ending_total_batch_cost', self::STRING_TYPE, 'Ending Total Batch Cost'),
            (array) new ExcelColumn('selisih_batch_cost', self::STRING_TYPE, 'Selisih Batch Cost')
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
