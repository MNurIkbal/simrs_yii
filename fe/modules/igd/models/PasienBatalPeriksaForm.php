<?php

namespace app\modules\igd\models;

use Yii;

class PasienBatalPeriksaForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $username;
    public $password;
    public $alasan_batal;
    public $additional_data;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['pendaftaran_id', 'alasan_batal', 'username', 'password'], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'alasan_batal' =>  Yii::t('fe', 'Alasan Batal'),
            'username' => Yii::t('fe', 'Username'),
            'password' => Yii::t('fe', 'Password'),
        ];
    }

}
