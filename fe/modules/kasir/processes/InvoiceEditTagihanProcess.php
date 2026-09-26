<?php

/**
 * @author : zn
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class InvoiceEditTagihanProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
    return [
        'type'=>'button',
        'title' => \Yii::t('fe', 'Print Summary'),
        'icon' => 'fa fa-print',
        'attributes' => [
            'id' => 'print-summary-edit-tagihan',
            // 'data-options' => 'link',
            // 'class'=>'spa',
            // 'target'=>'_blank'
            // 'id' => 'print-detail-edit-tagihan',
            'data-popup'=>'tooltip',
            'data-toggle'=>'modal',
			'data-target' => '#modal_backdrop',
			'data-width' => '50%',
			'action' => '/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?multipayer=true&isdetail=false&id=',
        ]
    ];
    }
}