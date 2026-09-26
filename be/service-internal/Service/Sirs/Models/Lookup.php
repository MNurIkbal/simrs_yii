<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
class Lookup extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookup_m';
    }
}
