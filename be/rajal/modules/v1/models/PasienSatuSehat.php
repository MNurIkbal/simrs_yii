<?php

namespace app\modules\v1\models;

use Yii;

class PasienSatuSehat extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasien_satusehat_m';
    }
}
