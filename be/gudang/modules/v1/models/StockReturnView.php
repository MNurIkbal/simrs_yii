<?php

namespace app\modules\v1\models;

use Yii;

class StockReturnView extends \Doco\components\DocoActiveRecord
{
    const RETUR = 'RETUR_RESEP';
    const BATAL = 'BATAL_RESEP';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_stockreturn_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
