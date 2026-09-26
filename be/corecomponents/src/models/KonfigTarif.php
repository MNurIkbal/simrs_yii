<?php

namespace Doco\models;

class KonfigTarif extends \Doco\components\DocoActiveRecord
{

    public $akses_pengguna;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'konfigtarif_k';
    }

    
}
