<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoConstants;

class CetakInvoiceAdhy extends \Doco\processes\CetakInvoiceProcess
{
	protected function jenisCetakan()
   {
      if(!$this->isObat) {
         if (!empty($this->pasienadmisi_id)) {
            $this->dokTercetak = 'invoice-ri-adhy';
            $this->dokPath = 'invoice-ranap-adhy';
         } elseif($this->instalasi_id === DocoConstants::INST_ID_RD) {
            $this->dokTercetak = 'invoice-rd-adhy';
            $this->dokPath = 'invoice_rd_adhyaksa';
         }
         else {
            $this->dokTercetak = 'invoice-rj-adhy';
            $this->dokPath = 'invoice_adhyaksa';
         }
      }
      else {
         $this->dokTercetak = 'invoice-obat-alkes';
         $this->dokPath = 'invoice_obat';
      }
   }

   protected function processFlow()
   {
      $this->populateData();
      $this->getDetailTindakan();
      $this->cetakInvoice();
   }
}