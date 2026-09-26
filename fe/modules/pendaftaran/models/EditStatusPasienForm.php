<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 *
 *
 * @property int $pendaftaran_id
 * @property string $username
 * @property string $password
 * @property string $status_periksa
 *
 */

class EditStatusPasienForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $username;
    public $password;
    public $status_periksa;
    public $jenis;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'no_pendaftaran', 
                'username', 
                'password',
                'status_periksa',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[
                'pendaftaran_id',
                'jenis',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_pendaftaran' => 'No Pendaftaran',
            'pendaftaran_id' => 'Pendaftaran ID',
            'username' => 'Username',
            'password' => 'Password',
            'status_periksa' => 'Status Periksa',
            'jenis' => 'Jenis Pendaftaran',
        ];
    }

}
