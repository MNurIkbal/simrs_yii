<?php

namespace app\modules\v1\models;

use Yii;

class DokumenV extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokumen_v';
    }
}