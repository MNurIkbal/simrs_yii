<?php

namespace app\modules\gizi\models;

use Yii;

class NrsForm extends \yii\base\Model
{
    public $keterangan;
    public $skrining;
    public $skor;
    public $kategori;


    public function rules()
    {
        return [
            [
                ['keterangan', 'skrining', 'skor', 'kategori'], 'safe'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'keterangan' => 'Keterangan',
            'skrining' => 'Skrining',
            'skor' => 'Total Skor',
            'kategori' => 'Kategori',
        ];
    }
}
