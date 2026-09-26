<?php

namespace Integrasi\Service\MobileMhg\Models;

use Yii;

class DaftarTindakan extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daftartindakan_m';
    }
}
