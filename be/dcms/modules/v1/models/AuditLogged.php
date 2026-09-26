<?php

namespace app\modules\v1\models;

use Yii;

class AuditLogged extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'audit.logged_actions';
    }
}