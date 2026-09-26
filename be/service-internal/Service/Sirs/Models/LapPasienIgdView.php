<?php

/**
 * @Author: Rizal
 */

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LapPasienIgdView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpasienigd_v';
    }

}
