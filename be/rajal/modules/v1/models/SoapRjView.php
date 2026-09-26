<?php

/**
 * @Author: rizal@docotel.com
 * @Date:   2018-10-17 13:34:37
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

class SoapRjView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'soaprj_v';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ['soaprj_id','tindakanpelayanan_id'];
    }
}
