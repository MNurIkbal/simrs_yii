<?php

namespace app\modules\pendaftaran\models;

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

class PasienBatalPeriksaForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $username;
    public $password;
    public $alasan_batal;
    public $tgl_batal;
    public $total_tagihan;
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
                'alasan_batal',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[
                'alasan_batal',
                'pendaftaran_id',
                'total_tagihan',
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
            'tgl_batal' => 'Tanggal Batal',
            'alasan_batal' => 'Alasan Batal',
            'jenis' => 'Jenis Pendaftaran',
        ];
    }

}
