<?php

/**
 * @Author: Fajar Supriadi
 * @Date:   2021-12-31 
 * @Last Modified by:   Fajar Supriadi
 * @Last Modified time: 2021-12-31
 */

namespace app\modules\master\models;

use Yii;

class SpesialisForm extends \yii\base\Model
{
    public $spesialis_kode;
    public $spesialis_nama;
    public $spesialis_namalainnya;
    public $is_active;

    public function rules()
    {
         return [
            [[
                'spesialis_kode',
                'spesialis_nama', 
                'is_active',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'spesialis_namalainnya',
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'spesialis_kode' => \Yii::t('fe', 'Kode Spesialis'),
            'spesialis_nama' => \Yii::t('fe', 'Nama Spesialis'),
            'spesialis_namalainnya' => \Yii::t('fe', 'Nama Lain Spesialis'),
            'is_active' => \Yii::t('fe', 'Status'),
        ];
    }


}
