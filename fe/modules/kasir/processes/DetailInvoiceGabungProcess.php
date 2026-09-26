<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class DetailInvoiceGabungProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		return [
			'type' => 'button',
			'title' => \Yii::t('fe', 'Cetak Detail Invoice'),
			'icon' => 'fa fa-file-pdf-o',
			'method' => 'not-exist',
			'attributes' => [
				'id' => 'cetak-detail',
				'data-options'=>'modal',
				'data-target' => '#modal_backdrop',
				'data-width' => '85%',
				'data-url' => '/kasir/inf-gabung-invoice/show-popup?id=',
			],
		];
	}
}