<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class InfoRiwayatResep extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforiwayatresep_v';
    }
}