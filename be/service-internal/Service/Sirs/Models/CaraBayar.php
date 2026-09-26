<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class CaraBayar extends \Integrasi\Components\ActiveRepositories
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carabayar_m';
    }
}
