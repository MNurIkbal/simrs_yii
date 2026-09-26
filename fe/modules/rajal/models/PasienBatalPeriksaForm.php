<?php

namespace app\modules\rajal\models;

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
	public $username;
	public $password;
	public $alasan_batal;
	public $tgl_batal;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'username', 'password', 'alasan_batal'], 'required'
            ,'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
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
            'username' => 'Username',
            'password' => 'Password',
            'tgl_batal' => 'Tanggal Batal',
            'alasan_batal' => 'Alasan Batal',
        ];
    }

}
