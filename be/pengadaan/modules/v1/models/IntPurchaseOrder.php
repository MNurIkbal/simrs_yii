<?php

namespace app\modules\v1\models;

use Yii;

class IntPurchaseOrder extends \Doco\components\DocoActiveRecord
{
	const POS = 'POS';
    const POM = 'POM';
    const PBM = 'PBM';
    const PBS = 'PBS';
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_purchase_v';
    }
}