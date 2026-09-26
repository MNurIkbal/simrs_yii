<?php

namespace app\modules\igd\models;

use Yii;

class AturDokterForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $dokter_id;
    public $tgl_masukperiksa;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['pendaftaran_id', 'dokter_id', 'tgl_masukperiksa'], 
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran',
            'dokter_id' =>  Yii::t('fe', 'Dokter jaga'),
        ];
    }

}