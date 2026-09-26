<?php

namespace Doco\dcms\models;

use Yii;

class AksesPenggunaForm extends \yii\base\Model
{
    public $peranpengguna_id;
    public $modul_id;
    public $pegawai_nama;
    public $pegawai_id;
    public $loginpemakai_id;
    public $additional_data;
    public $nama_pemakai;

    public $akses_pemakai;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_pemakai','akses_pemakai'], 'required'],
            [['additional_data','akses_pemakai','loginpemakai_id'], 'safe'],
            ['nama_pemakai','checkPemakai'],
        ];
    }

    public function checkPemakai($attribute, $params)
    {
        if (!$this->loginpemakai_id) {
            $this->addError('nama_pemakai','Nama pegawai tidak boleh kosong');
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'peranpengguna_id' => Yii::t('fe','Peranpengguna'),
            'nama_pemakai' => Yii::t('fe','Nama pemakai'),
            'modul_id' => Yii::t('fe','Modul'),
            'loginpemakai_id' => Yii::t('fe','Loginpemakai'),
            'additional_data' => Yii::t('fe','Additional Data'),
        ];
    }
}