<?php

/**
 * @author : Setyabudi Dwisandi Arifin (setyabudi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use Extensions\kasir\DetailInvoiceBelumBayar;
use Extensions\kasir\InvoicePayerBelumBayar;
use Doco\processes\CetakDetailInvoiceProcess;

class CetakDetailInvoiceBg extends \Doco\processes\CetakDetailInvoiceProcess
{
   public $dokTercetak = 'invoice-pembayaran-mhbg';
   public $dokPath = 'detail-invoice-mhbg';

   protected function linkTo($outputAttributes = false)
   {
      $pathPenjamin = new CetakDetailInvoiceProcess;
      $pathPenjamin->dokPath = 'invoice-penjamin-mhbg';
      $pathPenjamin->pisahBill = false;
      $pathPenjamin->outputAttributes = $outputAttributes;
      return $pathPenjamin->execute();
   }

   protected function processFlow()
   {
      $request = $this->_requestData;
      $jenis_invoice = $request->get('jenis_invoice', 1);
      $type = $request->get('invoice_type', 'invoice');
      $outputAttributes = $request->get('outputAttributes', false);
      if ($type == 'tmp') {
         if ($jenis_invoice == self::INVOICE_PENJAMIN) {
            $pathPenjamin = new InvoicePayerBelumBayar;
            $pathPenjamin->dokPath = $this->dokPath;
            $pathPenjamin->pisahBill = true;
            return $pathPenjamin->execute();
         } else {
            return (new DetailInvoiceBelumBayar)->execute();
         }
      } else if ($type == 'invoice') {
         if ($jenis_invoice == self::INVOICE_PENJAMIN && !$outputAttributes) {
            $this->linkTo();
         }
         elseif($outputAttributes) {
            if ($jenis_invoice == self::INVOICE_PENJAMIN) {
               return $this->linkTo(true);
            }
            else {
               $this->populateData();
               $this->getDetailInvoice();
               return [
                  'attributes' => $this->setAttrPrint(),
                  'kode_doc' => $this->dokTercetak,
               ];
            }
         }
         else {
            $this->populateData();
            $this->getDetailInvoice();
            $this->cetakDetailInvoice();
         }
      }
   }
}