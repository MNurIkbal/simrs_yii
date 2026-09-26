<?php

namespace app\modules\pendaftaran\models;

use Yii;


class MergeRekammedikForm extends \yii\base\Model
{
    public $no_rekammedik_asal;
    public $no_rekammedik_tujuan;
    public $username;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'no_rekammedik_asal',
                    'no_rekammedik_tujuan', 
                    'username', 
                    'password', 
                ],
                'required', 'on' => 'save', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong'),
            ],
            [[
                'no_rekammedik_asal', 
                'no_rekammedik_tujuan',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_rekammedik_asal' => Yii::t('fe', 'No Rekam Medik Asal'),
            'no_rekammedik_tujuan' => Yii::t('fe', 'No Rekam Medik Tujuan'),
        ];
    }
}
