<?php

namespace app\modules\v1\models;

use Yii;

class RencanaOperasiView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rencanaoperasi_v';
    }

    public static function primaryKey()
    {
        return ['rencanaoperasi_id'];
    }
}
