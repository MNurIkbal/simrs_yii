<?php

namespace Doco\dcms\models;

use Yii;

class UserManagementForm extends \yii\base\Model
{

    public $nama_pengguna;
    public $peranpenggunanamalain;
    public $peranpengguna_aktif = 1;
    public $modul_id;
    public $peranpengguna_menu;
    public $peranpengguna_akses;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_pengguna', 'peranpenggunanamalain','peranpengguna_aktif','modul_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nama_pengguna' => Yii::t('fe','Nama pengguna'),
            'peranpenggunanamalain' => Yii::t('fe','Peran pengguna namalain'),
            'peranpengguna_aktif' => Yii::t('fe','Peranpengguna aktif'),
            'modul_id' => Yii::t('fe','Modul'),
        ];
    }
}
