<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "instalasi_v".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $instalasi_singkatan
 */
class InstalasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instalasi_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'instalasi_nama', 'instalasi_singkatan'], 'default', 'value' => null],
            [['instalasi_id',], 'integer'],
            [['instalasi_nama', 'instalasi_singkatan'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama Instalasi'),
            'instalasi_singkatan' => Yii::t('app', 'Singkatan Instalasi'),
        ];
    }    
}
