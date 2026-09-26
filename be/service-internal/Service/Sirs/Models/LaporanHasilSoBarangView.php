<?php
namespace Integrasi\Service\Sirs\Models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Integrasi\Components\DocoExcelActiveRecord;

class LaporanHasilSoBarangView extends DocoExcelActiveRecord {
    public static function tableName() {
        return 'laporanhasilsobarang_v';
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('tgl_form_so', self::DATE_EXCEL, 'Tanggal Form SO'),
            (array) new ExcelColumn('tgl_validasi_so', self::DATE_EXCEL, 'Tanggal Validasi SO'),
            (array) new ExcelColumn('tgl_implementasi', self::DATE_EXCEL, 'Tanggal Implementasi SO'),
            (array) new ExcelColumn('validasi_by', self::STRING_TYPE, 'Divalidasi Oleh'),
            (array) new ExcelColumn('no_form_so', self::STRING_TYPE, 'No. Form SO'),
            (array) new ExcelColumn('instalasi_ruangan', self::STRING_TYPE, 'Instalasi - Ruangan'),
            (array) new ExcelColumn('kelompokbarang_nama', self::STRING_TYPE, 'Kelompok Barang'),
            (array) new ExcelColumn('subkelompok_nama', self::STRING_TYPE, 'Sub Kelompok'),
            (array) new ExcelColumn('kode_barang', self::STRING_TYPE, 'Kode Barang'),
            (array) new ExcelColumn('nama_barang', self::STRING_TYPE, 'Nama Barang'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan Kecil'),
            (array) new ExcelColumn('harganetto', self::STRING_TYPE, 'Harga Netto (Rp.)'),
            (array) new ExcelColumn('stok_sistem', self::STRING_TYPE, 'Stok Sistem'),
            (array) new ExcelColumn('stok_fisik', self::STRING_TYPE, 'Stok Fisik'),
            (array) new ExcelColumn('selisih', self::STRING_TYPE, 'Selisih'),
            (array) new ExcelColumn('stok_akhir', self::STRING_TYPE, 'Stok Akhir'),
            (array) new ExcelColumn('selisih_akhir', self::STRING_TYPE, 'Selisih Akhir'),
            (array) new ExcelColumn('total_harga_netto', self::STRING_TYPE, 'Total Harga Netto (Rp.)'),
            (array) new ExcelColumn('total_harga_selisih', self::STRING_TYPE, 'Total Harga Selisih (Rp.)')
        ];
    }
}
