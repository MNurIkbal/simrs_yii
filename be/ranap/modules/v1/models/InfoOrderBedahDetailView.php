<?php

namespace app\modules\v1\models;

use Yii;

class InfoOrderBedahDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoorderanbedahdetail_v';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return [""];
    }
}
