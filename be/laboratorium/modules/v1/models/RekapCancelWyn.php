<?php

namespace app\modules\v1\models;

use Yii;

class RekapCancelWyn extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logbatallab_wynacom_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'status', 
                'ordernumber',
                'testid',
                'testname',
                'userid',
                'username',
                'reason',
                'status_response',
                'messages',
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }
}
