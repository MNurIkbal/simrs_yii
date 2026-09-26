<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;

class DetailInvoiceAdhyaksa extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		return [
         'title' => Yii::t('fe', 'Cetak Invoice'),
         'icon' => 'fa fa-print',
         'method' => '#',
         'attributes' => [
            'id'=>'cetak-detail-invoice-adhy',
            'data-options' => 'link',
         ]
      ];
	}
}