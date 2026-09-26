<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanPtmEkgV extends  \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanptmekg_v';
    }
}