<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LapPurchaseRequisitionOutstandingView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanproutstanding_v';
    }
}