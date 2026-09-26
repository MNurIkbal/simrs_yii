<?php

namespace Doco\models\pendaftaran;

use Yii;
class JadwalCuti extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwalcuti_m';
    }
}