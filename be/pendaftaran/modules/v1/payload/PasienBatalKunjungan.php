<?php

namespace app\modules\v1\payload;

use Yii;

class PasienBatalKunjungan extends \yii\base\Model
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $password;
    public $alasan_batal;
    public $jenis;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'no_pendaftaran', 
                    'alasan_batal', 
                    'password'
                ],'required', 'message'=>'{attribute} Tidak boleh kosong'
            ],
            [['no_pendaftaran', 'jenis'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'alasan_batal' => 'Alasan batal',
            'username' =>'username',
            'password' =>'password',
        ];
    }

}
