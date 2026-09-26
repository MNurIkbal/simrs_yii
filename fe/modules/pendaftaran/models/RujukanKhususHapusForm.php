<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 *
 * @property int $idrujukan
 * @property string $username
 * @property string $password
 *
 */

class RujukanKhususHapusForm extends \yii\base\Model
{
    public $idrujukan;
    public $norujukan;
    public $username;
    public $password;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'idrujukan', 
                'norujukan',
                'username', 
                'password',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idrujukan' => 'ID Rujukan',
            'norujukan' => 'No Rujukan',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

}