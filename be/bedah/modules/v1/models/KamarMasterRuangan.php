<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 14:49:58
 */

namespace app\modules\v1\models;

use Yii;
class KamarMasterRuangan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'masterkamarruangan_v';
    }

    public static function primaryKey()
    {
        return ['ruangan_id'];
    }
}