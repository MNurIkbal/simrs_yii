<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;

class DetailInvoiceAdhy extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      return [
         'type'=>'button',
         'title' => Yii::t('fe', 'Cetak Invoice'),
         'icon' => 'fa fa-print',
         'method' => 'not-exist',
         'attributes' => [
             'id'=>'cetak-detail-invoice',
             'data-options' => 'click',
             'target'=>'_blank',
         ]
      ];
	}
}