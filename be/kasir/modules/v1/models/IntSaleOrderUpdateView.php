<?php

namespace app\modules\v1\models;

use Yii;

class IntSaleOrderUpdateView extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\OdooRepositories';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_saleorderupdate_v';
    }
}
