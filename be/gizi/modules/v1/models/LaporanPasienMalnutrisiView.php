<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-26 14:54:46
 */

namespace app\modules\v1\models;

use Yii;

class LaporanPasienMalnutrisiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanmalnutrisi_v';
    }
}