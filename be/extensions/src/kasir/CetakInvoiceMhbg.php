<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoConstants;

class CetakInvoiceMhbg extends \Doco\processes\CetakInvoiceProcess
{
	protected function jenisCetakan()
   {
      if (!empty($this->pasienadmisi_id)) {
         $this->dokTercetak = 'invoice-ri-mhbg';
         $this->dokPath = 'invoice-ranap-mhbg';
      } elseif($this->instalasi_id === DocoConstants::INST_ID_RD) {
         $this->dokTercetak = 'invoice-igd-mhbg';
         $this->dokPath = 'invoice-rd-mhbg';
      }
      else {
         $this->dokTercetak = 'invoice-pembayaran-mhbg';
         $this->dokPath = 'invoice-rajal-mhbg';
      }
   }

   protected function getDetailTindakan()
   {
      $detail = $this->getDetail();
      $dataDokter = $data = [];
      $arrDokter = '';
      $total = 0;
      if(!empty($detail)) {
         foreach ($detail as $key => $value) {
            $kelompok_tindakan = $value['kelompoktindakan_nama'];
            $data[$kelompok_tindakan][] = $value;
            $total = $total + $value['tarif_satuan'];
            $dataDokter[] = isset($value['dokter_tindakan']) ? $value['dokter_tindakan'] : null;
         }
         $arrDokter = "" . implode(", ", $dataDokter) . "";
      }
      $this->dataTindakan = $data;
      $this->arrDokter = $arrDokter;
      $this->dataDokter = $dataDokter;
   }

   protected function processFlow()
   {
      $this->jenisCetakan();
      $this->populateData();
      $this->getDetailTindakan();
      $this->cetakInvoice();
   }
}