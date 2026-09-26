<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;
use yii\helpers\ArrayHelper;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;

class LaporanPurchaseRequisitionView extends \Doco\components\DocoExcelActiveRecord {
    const DATERANGE_TYPE = 'daterange';
    const DATE_TYPE = 'date';
    const STRING_TYPE = 'string';
    const NUMBER_TYPE = 'number';
    const DATE_FORMAT = 'd M Y';

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'laporanpenerimaanpopr_v';
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

    public function columnNames() {
        return [
            (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'Nomor PR'),
            (array) new ExcelColumn('tanggal_pr', self::DATE_TYPE, 'Tanggal PR'),
            (array) new ExcelColumn('jenis_pr', self::STRING_TYPE, 'Jenis PR'),
            (array) new ExcelColumn('status_pr', self::STRING_TYPE, 'Status PR'),
            (array) new ExcelColumn('alasan_batal_pr', self::STRING_TYPE, 'Alasan Batal PR'),
            (array) new ExcelColumn('no_po', self::STRING_TYPE, 'Nomor PO'),
            (array) new ExcelColumn('tgl_verifikasi', self::DATE_TYPE, 'Tanggal Verifikasi PO'),
            (array) new ExcelColumn('vendor_obat', self::STRING_TYPE, 'Vendor'),
            (array) new ExcelColumn('nama_obat', self::STRING_TYPE, 'Nama Obat'),
            (array) new ExcelColumn('qty_po', self::STRING_TYPE, 'Qty PO'),
            (array) new ExcelColumn('qty_terima', self::STRING_TYPE, 'Qty Terima'),
            (array) new ExcelColumn('nilai_konversi', self::STRING_TYPE, 'Nilai Konversi'),
            (array) new ExcelColumn('hna', self::STRING_TYPE, 'Harga (Rp.)'),
            (array) new ExcelColumn('disc', self::STRING_TYPE, 'Diskon (%)'),
            (array) new ExcelColumn('ppn', self::STRING_TYPE, 'PPn (%)'),
            (array) new ExcelColumn('harga_akhir', self::STRING_TYPE, 'Harga Akhir (Rp.)'),
            (array) new ExcelColumn('status_po', self::STRING_TYPE, 'Status PO'),
            (array) new ExcelColumn('catatan_1', self::STRING_TYPE, 'Catatan 1'),
            (array) new ExcelColumn('catatan_2', self::STRING_TYPE, 'Catatan 2'),
            (array) new ExcelColumn('alasan_batal_po', self::STRING_TYPE, 'Alasan Batal PO'),
            (array) new ExcelColumn('no_penerimaan', self::STRING_TYPE, 'Nomor Penerimaan'),
            (array) new ExcelColumn('tanggal_penerimaan', self::DATE_TYPE, 'Tanggal Penerimaan'),
        ];
    }
}
