<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kamar Form
 * @copyright 21 Mei 2018 aweutist
 */

namespace Doco\master\models;

use Yii;

class NotifForm extends \yii\base\Model
{
    public $notifikasi;
    public $judul_temp;

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['notifikasi', 'judul_temp'], 'required'],
        ];
    }

    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'notifikasi' => \Yii::t('fe', 'Notifikasi'),
            'judul_temp' => \Yii::t('fe', 'Judul Template'),
        ];
    }

}
