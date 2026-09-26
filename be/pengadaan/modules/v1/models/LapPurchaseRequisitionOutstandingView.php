<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\classes\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;

class LapPurchaseRequisitionOutstandingView extends DocoExcelActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporanproutstanding_v';
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
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'Nomor PR'),
            (array) new ExcelColumn('tgl_pr', self::DATE_EXCEL, 'Tanggal PR'),
            (array) new ExcelColumn('kode_obat', self::STRING_TYPE, 'Kode Obat'),
            (array) new ExcelColumn('obatalkes_nama', self::STRING_TYPE, 'Nama Obat'),
            (array) new ExcelColumn('jenis_obat', self::STRING_TYPE, 'Jenis Obat'),
            (array) new ExcelColumn('qty_input', self::STRING_TYPE, 'Qty'),
            (array) new ExcelColumn('satuan', self::STRING_TYPE, 'Satuan'),
            (array) new ExcelColumn('uom', self::STRING_TYPE, 'UoM'),
            (array) new ExcelColumn('status_pr', self::STRING_TYPE, 'Status PR'),
            (array) new ExcelColumn('manufaktur_nama', self::STRING_TYPE, 'Manufaktur'),
            (array) new ExcelColumn('status_obat', self::STRING_TYPE, 'Status Obat'),
            (array) new ExcelColumn('catatan_pr', self::STRING_TYPE, 'Catatan'),
            (array) new ExcelColumn('alasan', self::STRING_TYPE, 'Alasan Batal'),
            (array) new ExcelColumn('pegawai', self::STRING_TYPE, 'Dibuat Oleh')
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
