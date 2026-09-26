<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;
use app\components\DocoHelpers;

class ButtonDetailRincianKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      return [
         'type' => 'button',
         'title' => 'Cetak Detail Rincian',
         'icon' => 'fa fa-file-pdf-o',
         'method' => 'not-exist',
         'attributes' => [
            'id'=>'cetak-detail-rincian-tagihan',
            'data-options' => 'modal',
            'data-target' => '#modal_backdrop',
            'data-width' => '50%',
            'data-url' => '/kasir/inf-pasien-belum-bayar/show-popup?id=',
            'data-conditions' => 'ref_pendaftaran_id,instalasi_id,no_pendaftaran',
         ]
      ];
	}
}
