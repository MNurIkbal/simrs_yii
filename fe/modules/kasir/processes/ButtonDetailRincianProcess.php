<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;
use app\components\DocoHelpers;

class ButtonDetailRincianProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		return [
         'type' => 'button',
         'title' => 'Cetak Detail Rincian',
         'icon' => 'fa fa-file-pdf-o',
         'method' => '#',
         'attributes' => [
            'data-options' => 'link',
            'id' => 'cetak-detail-rincian-tagihan-designer',
         ]
      ];
	}
}