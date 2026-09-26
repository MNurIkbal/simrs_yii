<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\models\kasir\InvoiceSudahBayarDetailView;
use Doco\models\kasir\InvoiceObatDetailView;

class CetakInvoiceProcess extends \Doco\processes\CetakDetailInvoiceProcess
{
   public $dokTercetak = 'invoice-pembayaran-rj';
   public $dokPath = 'invoice';

   protected function jenisCetakan()
   {
      if(!$this->isObat) {
         if (!empty($this->pasienadmisi_id)) {
            $this->dokTercetak = 'invoice-pembayaran-ri';
            $this->dokPath = 'invoice-ranap';
         } elseif($this->instalasi_id === DocoConstants::INST_ID_RD) {
            $this->dokTercetak = 'invoice-pembayaran-rd';
         }
      }
      else {
         $this->dokTercetak = 'invoice-obat-alkes';
         $this->dokPath = 'invoice_obat';
      }
   }

   protected function getDetailTindakan()
   {
      if(empty($this->invoice_id)) {
         return [];
      }

      $dataDokter = [];
      $arrDokter = '';
      $total = 0;

      if(!$this->isObat) {
         if(!empty($this->pasienadmisi_id)) {
            $data = InvoiceSudahBayarDetailView::find()
               ->select([
                  'kelompoktindakan_nama', 'SUM(tarif_dijamin) AS tarif_dijamin', 'SUM(tarif_dibayarkan) AS tarif_dibayarkan', 
                  'SUM(tarif_diskon) AS tarif_diskon', 'SUM(sub_total) AS sub_total'])
               ->where(['pembayaran_id' => $this->invoice_id, 'is_diskon' => false])
               ->groupBy(['kelompoktindakan_nama'])
               ->asArray()->all();
         }
         else {
            $detail = InvoiceSudahBayarDetailView::find()
               ->select([
                  'tgl_pelayanan', 'kelompoktindakan_id','kelompoktindakan_nama','tindakan_obat_id','tindakan_obat_nama',
                  'qty','tarif_satuan','tarif_diskon','sub_total','dokter_tindakan','ruangan_id','ruangan_pelayanan','is_obat','is_konsultasi','is_visite',
                  'kelaspelayanan_id','kelaspelayanan_nama','tarif_cyto','tarif_dijamin','tarif_dibayarkan'
               ])
               ->where(['pembayaran_id' => $this->invoice_id, 'is_diskon' => false])
               ->asArray()->all();
            
            if(!empty($detail)) {
               foreach ($detail as $key => $value) {
                  $kelompokTindakan = ArrayHelper::getValue($value, 'kelompoktindakan_nama');
                  $tarifSatuan = ArrayHelper::getValue($value, 'tarif_satuan');
                  $dokterTindakan = ArrayHelper::getValue($value, 'dokter_tindakan');
                  $data[$kelompokTindakan][] = $value;
                  $total = $total + $tarifSatuan;
                  $dataDokter[] = $dokterTindakan;
               }
               $arrDokter = "" . implode(", ", $dataDokter) . "";
            }
         }
      }
      else {
         $data = InvoiceObatDetailView::find()
         ->where(['pembayaran_id' => $this->invoice_id])
         ->orderBy(['obatalkes_nama' => SORT_ASC])
         ->all();
      }
      
      $this->dataTindakan = $data;
      $this->arrDokter = $arrDokter;
      $this->dataDokter = $dataDokter;
   }

   protected function processFlow()
   {
      $this->populateData();
      $this->jenisCetakan();
      $this->getDetailTindakan();
      $this->cetakDetailInvoice();
   }
}
