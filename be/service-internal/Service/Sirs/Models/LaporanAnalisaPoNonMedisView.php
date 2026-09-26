<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */
namespace Integrasi\Service\Sirs\Models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoExcelActiveRecord;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;

class LaporanAnalisaPoNonMedisView extends DocoExcelActiveRecord
{
   /**
    * @inheritdoc
    */
   public static function tableName()
   {
      return 'lapanalisapononmedis_v';
   }

   public function columnNames()
   {
      return [
         (array) new ExcelColumn('kode_barang', self::STRING_TYPE, 'Kode Barang'),
         (array) new ExcelColumn('nama_barang', self::STRING_TYPE, 'Nama Barang'),
         (array) new ExcelColumn('no_pr', self::STRING_TYPE, 'No PR'),
         (array) new ExcelColumn('tgl_pr', self::DATE_EXCEL, 'Tanggal Buat PR'),
         (array) new ExcelColumn('qty_pr', self::STRING_TYPE, 'Qty PR'),
         (array) new ExcelColumn('satuan_pr', self::STRING_TYPE, 'UoM PR'),
         (array) new ExcelColumn('catatan', self::STRING_TYPE, 'Catatan'),
         (array) new ExcelColumn('no_po', self::STRING_TYPE, 'No. PO'),
         (array) new ExcelColumn('tgl_po', self::DATE_EXCEL, 'Tanggal Buat PO'),
         (array) new ExcelColumn('tgl_validasi_po', self::DATE_EXCEL, 'Tanggal Validasi PO'),
         (array) new ExcelColumn('tgl_batal_po', self::DATE_EXCEL, 'Tanggal Batal PO'),
         (array) new ExcelColumn('catatan_batal_po', self::STRING_TYPE, 'Catatan Batal PO'),
         (array) new ExcelColumn('qty_po', self::STRING_TYPE, 'Qty PO'),
         (array) new ExcelColumn('satuan_po', self::STRING_TYPE, 'UoM PO'),
         (array) new ExcelColumn('harga', self::STRING_TYPE, 'Harga (Rp.)'),
         (array) new ExcelColumn('diskon', self::NUMBER_TYPE, 'Diskon (%)'),
         (array) new ExcelColumn('ppn', self::NUMBER_TYPE, 'PPn (%)'),
         (array) new ExcelColumn('subtotal', self::STRING_TYPE, 'Subtotal (Rp.)'),
         (array) new ExcelColumn('harga_total', self::STRING_TYPE, 'Harga Total (Rp.)'),
         (array) new ExcelColumn('no_penerimaan', self::STRING_TYPE, 'No. Penerimaan'),
         (array) new ExcelColumn('tgl_penerimaan', self::DATE_EXCEL, 'Tanggal Penerimaan'),
         (array) new ExcelColumn('qty_penerimaan', self::STRING_TYPE, 'Qty Penerimaan'),
         (array) new ExcelColumn('penerimaan', self::STRING_TYPE, 'UoM Penerimaan'),
         (array) new ExcelColumn('sisa_penerimaan', self::STRING_TYPE, 'Sisa Penerimaan (PO Ballance)'),
         (array) new ExcelColumn('penerimaan', self::STRING_TYPE, 'UoM Sisa Penerimaan'),
         (array) new ExcelColumn('nofaktur_penerimaan', self::STRING_TYPE, 'No. Faktur Penerimaan'),
         (array) new ExcelColumn('tgl_verifikasi_penerimaan', self::DATE_EXCEL, 'Tanggal Verifikasi Penerimaan'),
         (array) new ExcelColumn('pr_jarak_po', self::STRING_TYPE, 'PR dibuat ke PO'),
         (array) new ExcelColumn('po_jarak_validasi_po', self::STRING_TYPE, 'PO dibuat ke Validasi PO'),
         (array) new ExcelColumn('po_jarak_tgl_penerimaan', self::STRING_TYPE, 'PO dibuat ke Tanggal Penerimaan'),
         (array) new ExcelColumn('pr_jarak_tgl_penerimaan', self::STRING_TYPE, 'PR dibuat ke Tanggal Penerimaan'),
         (array) new ExcelColumn('po_validasi_tgl_penerimaan', self::STRING_TYPE, 'PO divalidasi ke Tanggal Penerimaan'),
         (array) new ExcelColumn('kode_supplier', self::STRING_TYPE, 'Kode supplier'),
         (array) new ExcelColumn('nama_supplier', self::STRING_TYPE, 'Nama supplier'),
         (array) new ExcelColumn('catatan_po', self::STRING_TYPE, 'Catatan (Catatan PO)'),
      ];
   }
}
