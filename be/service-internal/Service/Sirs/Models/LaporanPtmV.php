<?php


namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanPtmV extends  \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanptm_v';
    }
}