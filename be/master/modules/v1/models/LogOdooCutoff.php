<?php

namespace app\modules\v1\models;

use Yii;

class LogOdooCutoff extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logodoocutoff_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_name', 'cutoff_date', 'triggered_by','generated_data','deleted_data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}