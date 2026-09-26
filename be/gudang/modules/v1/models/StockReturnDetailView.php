<?php

namespace app\modules\v1\models;

use Yii;

class StockReturnDetailView extends \Doco\components\DocoActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_stockreturndetail_v';
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
