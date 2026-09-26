<?php

namespace app\modules\v1\models;

use Yii;

class InfoTagihanPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function primaryKey()
	{
	    return ['pelayanan_id'];
	}

    public static function tableName()
    {
        return 'infotagihanpasien_v';
    }
}
