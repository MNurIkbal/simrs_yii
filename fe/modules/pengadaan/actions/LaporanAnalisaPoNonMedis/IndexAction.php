<?php

/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;
use app\components\DHtml;

class IndexAction extends Action
{
   public function run()
   {
      $module = $this->controller->_module;
      $columns = $this->getColumns();
      $titleMenu = DHtml::getTitleMenu();
      $title = !empty($titleMenu) ? $titleMenu : $this->controller->_title;
      return $this->controller->render('index', get_defined_vars());
   }

   private function getColumns()
   {
      return [
         Yii::t('fe', 'No'),
         Yii::t('fe', 'Kode Barang'),
         Yii::t('fe', 'Nama Barang'),
         Yii::t('fe', 'No PR'),
         Yii::t('fe', 'Tanggal Buat PR'),
         Yii::t('fe', 'Tanggal Approve PR'),
         Yii::t('fe', 'Qty PR'),
         Yii::t('fe', 'UoM PR'),
         Yii::t('fe', 'Catatan PR'),
         Yii::t('fe', 'No. PO'),
         Yii::t('fe', 'Cito'),
         Yii::t('fe', 'Admin'),
         Yii::t('fe', 'Tanggal Buat PO'),
         Yii::t('fe', 'Tanggal Validasi PO'),
         Yii::t('fe', 'Tanggal Batal PO'),
         Yii::t('fe', 'Catatan Batal PO'),
         Yii::t('fe', 'Qty PO'),
         Yii::t('fe', 'UoM PO'),
         Yii::t('fe', 'Harga (Rp.)'),
         Yii::t('fe', 'Diskon (%)'),
         Yii::t('fe', 'PPn (%)'),
         Yii::t('fe', 'Subtotal (Rp.)'),
         Yii::t('fe', 'Harga Total (Rp.)'),
         Yii::t('fe', 'No. Penerimaan'),
         Yii::t('fe', 'Tanggal Penerimaan'),
         Yii::t('fe', 'Qty Penerimaan'),
         Yii::t('fe', 'UoM Penerimaan'),
         Yii::t('fe', 'Sisa Penerimaan (PO Ballance)'),
         Yii::t('fe', 'UoM Sisa Penerimaan'),
         Yii::t('fe', 'No. Faktur Penerimaan'),
         Yii::t('fe', 'Tanggal Verifikasi Penerimaan'),
         Yii::t('fe', 'PR diapprove ke PO'),
         Yii::t('fe', 'PO dibuat ke Validasi PO'),
         Yii::t('fe', 'PO dibuat ke Tanggal Penerimaan'),
         Yii::t('fe', 'PR diapprove ke Tanggal Penerimaan'),
         Yii::t('fe', 'PO divalidasi ke Tanggal Penerimaan'),
         Yii::t('fe', 'Kode Supplier'),
         Yii::t('fe', 'Supplier (Nama supplier)'),
         Yii::t('fe', 'Catatan PO'),
      ];
   }
}
