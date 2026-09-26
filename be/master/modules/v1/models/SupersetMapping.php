<?php

namespace app\modules\v1\models;

use Yii;

class SupersetMapping extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'superset_mapping_m';
    }

}
