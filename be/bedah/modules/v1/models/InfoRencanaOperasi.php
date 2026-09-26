<?php

namespace app\modules\v1\models;

use Yii;

class InfoRencanaOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{rencanaoperasi_v}}';
    }
}