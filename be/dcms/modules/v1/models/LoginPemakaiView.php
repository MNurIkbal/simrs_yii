<?php

namespace app\modules\v1\models;

use Yii;

class LoginPemakaiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loginpemakai_v';
    }

    public function fields() 
    {
        $fields = parent::fields();
        if (in_array('katakunci_pemakai', $fields))
            unset($fields['katakunci_pemakai']);
        return $fields;
    }
}
