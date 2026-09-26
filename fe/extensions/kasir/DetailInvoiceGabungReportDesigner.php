<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;

class DetailInvoiceGabungReportDesigner extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		return [
			'type' => 'button',
			'title' => \Yii::t('fe', 'Cetak Detail Invoice'),
			'icon' => 'fa fa-file-pdf-o',
			'method' => 'not-exist',
			'attributes' => [
				'id' => 'cetak-detail-gabung',
				'data-options'=>'modal',
				'data-target' => '#modal_backdrop',
				'data-width' => '50%',
                'data-url' => '/kasir/inf-gabung-invoice/show-popup-cetakan?id=',
                'data-conditions'=>'ref_pembayaran_id,invoicegabung_id'
			],
		];
	}
}