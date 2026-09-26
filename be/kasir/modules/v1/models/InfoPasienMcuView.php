<?php

namespace app\modules\v1\models;

use Yii;

class InfoPasienMcuView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasienmcu_v';
    }
}
