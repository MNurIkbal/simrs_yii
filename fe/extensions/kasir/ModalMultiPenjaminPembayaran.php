<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;

class ModalMultiPenjaminPembayaran extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      return [
         'type' => 'button',
         'title' => \Yii::t('fe', 'Cetak Detail Invoice'),
         'icon' => 'fa fa-print',
         'method' => 'not-exist',
         'attributes' => [
            'id' => 'btn-cetak-detail-invoice-pembayaran',
            'data-target' => '#modal_backdrop',
            'data-options'=>'link',
            'data-width' => '50%',
            'data-toggle' => 'modal',
         ],
      ];
	}
}