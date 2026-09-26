<?php

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\master\models;

use Yii;

class PencarianPasienForm extends \yii\base\Model
{
    public $jenis_transaksi;

    public function rules()
    {
        return [
            [ ['jenis_transaksi'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            
        ];
    }
}