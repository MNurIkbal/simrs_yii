<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanPurchaseOrderOutstandingView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpooutstanding_v';
    }
}