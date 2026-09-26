<?php

namespace Doco\models\bpjs;

use Yii;

class LoginJknR extends \Doco\components\DocoActiveRecord
{

    public static function getDb() 
    {
        return \Yii::$app->db_integration;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logjkn_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'state', 'created_date','created_by','payload','sync_respon'], 'safe'],
        ];
    }
}
