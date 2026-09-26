<?php

namespace Doco\dcms\models;

use Yii;

class PeranPenggunaForm extends \yii\base\Model
{

    public $peranpenggunanama;
    public $peranpenggunanamalain;
    public $peranpengguna_aktif = 1;
    public $is_exception = 0;
    public $modul_id;
    public $peranpengguna_menu;
    public $peranpengguna_akses;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['peranpenggunanama', 'peranpenggunanamalain','peranpengguna_aktif', 'is_exception','modul_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'peranpenggunanama' => Yii::t('fe','Peran pengguna nama'),
            'peranpenggunanamalain' => Yii::t('fe','Peran pengguna namalain'),
            'peranpengguna_aktif' => Yii::t('fe','Peranpengguna aktif'),
            'is_exception' => Yii::t('fe','Exception Test'),
            'modul_id' => Yii::t('fe','Modul'),
        ];
    }
}
