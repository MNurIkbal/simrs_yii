<?php

namespace app\modules\v1\models;

use Yii;

class IntPurchaseOrderLine extends \Doco\components\DocoActiveRecord
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
        return 'int_purchasedetail_v';
    }
}