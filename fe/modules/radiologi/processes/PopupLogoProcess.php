<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\radiologi\processes;

use Yii;

class PopupLogoProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		return [
			'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
			'icon' => 'fa fa-print',
			'attributes' => [
				'id' => 'btn-cetak',
				'target' => '_blank',
				'data-options' => 'link',
				'data-url' => '/radiologi/hasil-rad/cetak?id=',
				'disabled' => 'disabled',
			]
		];
	}
}