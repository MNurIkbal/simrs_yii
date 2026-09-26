<?php

namespace app\modules\v1\payload;

use Yii;


class MergeRekammedikPayload extends \yii\base\Model
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
                'required', 'message'=>'{attribute} Tidak boleh kosong',
            ],
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
            'username' =>'username',
            'password' =>'password',
        ];
    }
}
