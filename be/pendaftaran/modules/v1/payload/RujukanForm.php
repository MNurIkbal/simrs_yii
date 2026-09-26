<?php

namespace app\modules\v1\payload;

use Yii;

class RujukanForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    public $noSep;
    public $tglRujukan;
    public $ppkDirujuk;
    public $jnsPelayanan;
    public $catatan;
    public $diagRujukan;
    public $tipeRujukan;
    public $poliRujukan;
    public $user;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'noSep',
                    'tglRujukan',
                    'ppkDirujuk',
                    'jnsPelayanan',
                    'catatan',
                    'diagRujukan',
                    'tipeRujukan',
                    'poliRujukan',
                    'user'
                ], 
                'required'
            ],
            [
                [
                    'noSep', 'tglRujukan', 'ppkDirujuk', 'jnsPelayanan', 'catatan', 'diagRujukan', 'tipeRujukan', 'poliRujukan', 'user'
                ],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'noSep' => Yii::t('app', 'No. SEP'),
            'tglRujukan' => Yii::t('app', 'Tanggal Rujukan'),
            'ppkDirujuk' => Yii::t('app', 'PPK Dirujuk'),
            'jnsPelayanan' => Yii::t('app', 'Jenis Pelayanan'),
            'catatan' => Yii::t('app', 'Catatan'),
            'diagRujukan' => Yii::t('app', 'Diagnosa'),
            'tipeRujukan' => Yii::t('app', 'Tipe Rujukan'),
            'poliRujukan' => Yii::t('app', 'Poli Rujukan'),
            'user' => Yii::t('app', 'User')
        ];
    }
}
