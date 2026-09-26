<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class InvoiceProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
      return [
        'title' => Yii::t('fe', 'Cetak Invoice'),
        'icon' => 'fa fa-print',
        'method' => '#',
        'attributes' => [
           'id'=>'cetak-invoice',
           'data-options' => 'link',
          //  'target'=>'_blank',
          'class' => 'spa'
        ]
      ];
    }
}