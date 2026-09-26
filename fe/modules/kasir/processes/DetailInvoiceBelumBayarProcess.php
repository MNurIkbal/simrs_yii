<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class DetailInvoiceBelumBayarProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
      return [
         'type' => 'button',
         'title' => Yii::t('fe', 'Cetak Detail Invoice'),
         'icon' => 'fa fa-print',
         'attributes' => [
            'id' => 'cetak-invoice-belum-bayar',
            'data-toggle' => 'modal',
            'data-target' => '#modal_backdrop',
            'data-width' => '50%',
         ]
      ];
    }
}