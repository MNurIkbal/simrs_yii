<?php

/**
 * @author : zn
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class InvoiceEditTagihanDetailProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
	{
		return [
			'type' => 'button',
			'title' => \Yii::t('fe', 'Print Detail Rincian'),
			'icon' => 'fa fa-print',
			// 'method' => 'not-exist',
			'attributes' => [
				'id' => 'print-detail-edit-tagihan',
                'data-popup'=>'tooltip',
                'data-toggle'=>'modal',
				'data-target' => '#modal_backdrop',
				'data-width' => '50%',
				'action' => '/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?multipayer=true&id=',
			],
		];
	}
}