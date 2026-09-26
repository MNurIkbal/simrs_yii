<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanPurchaseOrderOutstandingBarangView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpooutstandingbarang_v';
    }
}