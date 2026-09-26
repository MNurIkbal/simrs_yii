<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;

class InvoiceKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
         return [
			'type' => 'button',
			'title' => \Yii::t('fe', 'Cetak Invoice'),
			'icon' => 'fa fa-print',
			'method' => 'not-exist',
			'attributes' => [
				'id' => 'btn-cetak-invoice',
				'data-options'=>'modal',
				'data-target' => '#modal_backdrop',
				'data-width' => '50%',
				'data-url' => '/kasir/inf-pasien-sudah-bayar/show-popup?multipayer=true&is_bgprocess=false&is_invoice=true&id=',
				'data-conditions'=>'pembayaran_id,penjamin_id,groupcarabayar_id,penjualanresep_id'
			],
		];
	}
}
