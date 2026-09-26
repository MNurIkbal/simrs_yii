<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-17 11:19:41
 */

namespace app\modules\v1\models;

use Yii;

class PasienKunjunganAkhirView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasienkunjunganakhir_v';
    }
}