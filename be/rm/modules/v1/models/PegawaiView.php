<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 16:44:25
 */

namespace app\modules\v1\models;

use Yii;

class PegawaiView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pegawai_v';
    }
}