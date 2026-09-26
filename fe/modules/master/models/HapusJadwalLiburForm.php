<?php

namespace app\modules\master\models;

use Yii;
use app\components\DocoBaseModel;

class HapusJadwalLiburForm extends DocoBaseModel
{
    public $jadwallibur_id;
    public $username;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'jadwallibur_id',
                'username',
                'password',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'jadwallibur_id',
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
            'jadwallibur_id' => Yii::t('fe','Jadwal Cuti ID'),
            'username' => Yii::t('fe','Username'),
            'password' => Yii::t('fe','Password'),
        ];
    }

}
