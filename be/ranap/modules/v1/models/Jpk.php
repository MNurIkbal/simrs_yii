<?php

namespace app\modules\v1\models;

use Yii;

class Jpk extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jpk_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
    	return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
    	return [];
    }
}