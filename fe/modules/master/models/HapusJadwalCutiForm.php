<?php

namespace app\modules\master\models;

use Yii;
use app\components\DocoBaseModel;

class HapusJadwalCutiForm extends DocoBaseModel
{
    public $jadwalcuti_id;
    public $username;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'jadwalcuti_id',
                'username',
                'password',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'jadwalcuti_id',
                'username',
                'password',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwalcuti_id' => Yii::t('fe','Jadwal Cuti ID'),
            'username' => Yii::t('fe','Username'),
            'password' => Yii::t('fe','Password'),
        ];
    }

}
