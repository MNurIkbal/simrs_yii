<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\radiologi;

use Yii;

class PopupLogo extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		return [
			'type' => 'button',
			'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
			'icon' => 'fa fa-print',
			'method' => 'not-exist',
			'attributes' => [
				'id' => 'btn-cetak-pemeriksaan',
				'data-options'=>'modal',
				'data-target' => '#modal_backdrop',
				'data-width' => '50%',
				'data-url' => '/radiologi/informasi-pasien-rad/show-popup?id=',
			]
		];
	}
}
