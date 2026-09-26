<?php

namespace app\modules\v1\models;

use Yii;

class InfoDataKunjunganView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infodatakunjungan_v';
    }

    public static function primaryKey(){
        return ['pendaftaran_id'];
    }
}
