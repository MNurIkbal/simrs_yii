<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "lookuptransaksi_m".
 *
 */
class LookupTransaksi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookuptransaksi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
