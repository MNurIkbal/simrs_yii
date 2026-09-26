<?php

namespace app\modules\v1\models;

use Yii;

class NotifikasiJobOrderView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifikasijoborder_v';
    }
}