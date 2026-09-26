<?php

namespace app\modules\v1\models;

use Yii;

class IntPemberianPiutangView extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\OdooRepositories';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_pemberianpiutang_v';
    }
}
