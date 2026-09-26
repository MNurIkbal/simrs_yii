<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 *
 * @property int $rujukanbpjs_id
 * @property string $username
 * @property string $password
 *
 */

class HapusRujukanForm extends \yii\base\Model
{
    public $rujukanbpjs_id;
    public $username;
    public $password;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'rujukanbpjs_id', 
                'username', 
                'password',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rujukanbpjs_id' => 'Rujukan BPJS ID',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

}
