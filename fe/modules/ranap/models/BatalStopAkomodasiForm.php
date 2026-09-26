<?php

/**
 * @Author: Ilhamsyah Pramono
 * @Date:   2022-12-15 00:28:09
 */

namespace app\modules\ranap\models;

use Yii;

class BatalStopAkomodasiForm extends \yii\base\Model
{
	public $pendaftaran_id;
	public $username;
	public $password;
	public $alasan_batalstop;
	public $tgl_batal;
    public $is_ditagihkan;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'username', 'password', 'alasan_batalstop' , 'is_ditagihkan'], 'required'
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
            'alasan_batalstop' => 'Alasan Batal',
            'is_ditagihkan' => 'Jenis Pembatalan'
        ];
    }
}
