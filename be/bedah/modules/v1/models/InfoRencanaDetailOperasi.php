<?php

namespace app\modules\v1\models;

use Yii;

class InfoRencanaDetailOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{rencanaoperasidetail_v}}';
    }
}