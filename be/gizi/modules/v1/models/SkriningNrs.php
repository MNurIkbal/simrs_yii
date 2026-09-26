<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-22 16:27:36
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-22 16:29:30
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

class SkriningNrs extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'skriningnrs_m';
    }
}
