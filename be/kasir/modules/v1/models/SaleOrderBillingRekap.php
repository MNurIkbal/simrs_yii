<?php

namespace app\modules\v1\models;

use Yii;

class SaleOrderBillingRekap extends \app\components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_billing_r';
    }
}
