<?php

namespace app\modules\v1\models;

use Yii;

class SaleOrderLineDetail extends \app\components\ActiveRepositories
{

    public $_repositori = 'app\components\repositories\OdooRepositories';

    public static function tableName()
    {
        return 'saleorder_linedetail_v';
    }
}
