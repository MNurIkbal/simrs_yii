<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $pendaftaran_id
 * @property string $username
 * @property string $password
 * @property string $alasan_batal
 * @property date $tgl_batal
 *
 */

class BatalBayarUangMukaForm extends \app\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $username;
    public $password;
    public $is_tunai;
    public $alasan_batal;
    public $bayaruangmuka_id;
    protected $xssProtected = [
        'alasan_batal'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'username', 
                'password',
                'alasan_batal',
                'is_tunai',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[
                'alasan_batal',
                'pendaftaran_id',
                'tanggal_batal',
                'is_tunai',
                'bayaruangmuka_id',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'username' => 'Username',
            'password' => 'Password',
            'alasan_batal' => 'Alasan Batal',
            'is_tunai' => 'Jenis Pembayaran',
        ];
    }

}
