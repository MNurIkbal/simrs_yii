<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;

class LaporanAdjustmentBarangView extends DocoExcelActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanadjustmenbarang_v';
    }

    public function columnNames()
    {
        return [
            (array) new ExcelColumn('ruangan_nama', self::STRING_TYPE, 'Ruangan'),
            (array) new ExcelColumn('no_adjusmen', self::STRING_TYPE, 'No. Transaksi'),
            (array) new ExcelColumn('tgl_adjusmen', self::DATE_EXCEL, 'Tanggal Adjustment'),
            (array) new ExcelColumn('jenis_adjusmen_nama', self::STRING_TYPE, 'Jenis Adjustment'),
            (array) new ExcelColumn('barang_kode', self::STRING_TYPE, 'Kode barang'),
            (array) new ExcelColumn('barang_nama', self::STRING_TYPE, 'Nama Barang'),
            (array) new ExcelColumn('qty', self::NUMBER_TYPE, 'Qty'),
            (array) new ExcelColumn('satuan_besar', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('qty_konversi', self::NUMBER_TYPE, 'Qty Konversi'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan Terkecil'),
            (array) new ExcelColumn('pegawai_adjusmen', self::STRING_TYPE, 'Nama Pegawai')
        ];
    }
}
