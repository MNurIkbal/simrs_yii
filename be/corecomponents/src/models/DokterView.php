<?php

namespace Doco\models;

use Yii;

class DokterView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokter_v';
    }
}
