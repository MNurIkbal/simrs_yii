<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Integrasi\Components\DocoExcelActiveRecord;

class LaporanPemakaianBarangView extends DocoExcelActiveRecord
{
    public static function tableName()
    {
        return 'laporanpemakaianbarang_v';
    }

    public function columnNames()
    {
        return [
            (array) new ExcelColumn('ruangan_nama', self::STRING_TYPE, 'Nama Ruangan'),
            (array) new ExcelColumn('tgl_transaksi', self::STRING_TYPE, 'Tanggal Transaksi'),
            (array) new ExcelColumn('no_transaksi', self::DATE_EXCEL, 'No Transaksi'),
            (array) new ExcelColumn('kelompokbarang_nama', self::DATE_EXCEL, 'Kelompok Barang'),
            (array) new ExcelColumn('barang_kode', self::STRING_TYPE, 'Kode Barang'),
            (array) new ExcelColumn('barang_nama', self::STRING_TYPE, 'Nama Barang'),
            (array) new ExcelColumn('qty', self::STRING_TYPE, 'Qty'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('harga_netto', self::NUMBER_TYPE, 'Harga Satuan (Rp)'),
            (array) new ExcelColumn('total_harga', self::STRING_TYPE, 'Total Harga (Rp)'),
            (array) new ExcelColumn('user', self::NUMBER_TYPE, 'User'),
            (array) new ExcelColumn('catatan', self::STRING_TYPE, 'Catatan')
        ];
    }
}
