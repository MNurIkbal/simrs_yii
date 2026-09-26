<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-14 10:59:52
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 10:30:53
 */

namespace Doco\bedah\models;

class IntraTindakanLuarBedahForm extends \yii\base\Model
{
    public $tindakanluarbedah_id;
    public $keyUnique;
    public $tindakanluarbedah_nama;
    public $qtytindakan;

    public function rules()
    {
        return [
            [['tindakanluarbedah_id', 'keyUnique', 'tindakanluarbedah_nama', 'qtytindakan'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong']
        ];
    }

    public function attributeLabels()
    {
        return [
            'tindakanluarbedah_id'=>\Yii::t('fe', 'Nama Tindakan'),
            'qtytindakan' => 'Qty'
        ];
    }
}