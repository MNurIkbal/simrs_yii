<?php

namespace app\modules\v1\models;

use Yii;

class StockOutView extends \Doco\components\DocoActiveRecord
{
    const BMHP = 'BMHP';
    const RESEP = 'RESEP';
    const RACIKAN_BATAL = 'RESEP_RACIKAN';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_stockout_v';
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
