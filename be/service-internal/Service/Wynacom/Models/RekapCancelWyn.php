<?php

namespace Integrasi\Service\Wynacom\Models;

use Yii;

class RekapCancelWyn extends \Integrasi\Components\ActiveRepositories
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
