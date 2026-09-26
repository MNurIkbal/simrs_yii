<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class DetailInvoicePembayaranProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
      return [
         'title' => Yii::t('fe', 'Cetak Detail Invoice'),
         'icon' => 'fa fa-print',
         'method' => '#',
         'attributes' => [
            'id'=>'cetak-detail-invoice',
            'data-options' => 'link',
         ]
      ];
    }
}