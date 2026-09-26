<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-09 16:35:24
 */

namespace app\modules\v1\models;

use Yii;

class DataDokterView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'datadokter_v';
    }
}
