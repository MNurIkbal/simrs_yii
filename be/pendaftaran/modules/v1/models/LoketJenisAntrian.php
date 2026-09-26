<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "loketjenisantrian_mp".
 *
 * @property integer $loket_id
 * @property string $jenisantriandetail_id
 */
class LoketJenisAntrian extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loketjenisantrian_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loket_id', 'jenisantriandetail_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loket_id' => 'Loket ID',
            'jenisantriandetail_id' => 'Jenisantriandetail ID',
        ];
    }
}