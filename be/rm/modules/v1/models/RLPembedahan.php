<?php

namespace app\modules\v1\models;

use Yii;

class RLPembedahan extends \Doco\components\DocoActiveRecord
{
    public $total_tindakan;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rl3_6_pembedahandetail_v';
    }
}
