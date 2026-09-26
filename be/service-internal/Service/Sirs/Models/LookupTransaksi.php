<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
class LookupTransaksi extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookuptransaksi_m';
    }
}
