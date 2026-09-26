<?php

namespace Integrasi\Service\Mhg\Models;

use Yii;

class Shift extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift_m';
    }
}
