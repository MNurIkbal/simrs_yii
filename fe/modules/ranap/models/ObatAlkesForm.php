<?php

/**
 * @Author: Rizal, duplicate from master
 * @Date:   2018-07-05
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-06-11 15:51:57
 */

namespace app\modules\ranap\models;

use Yii;

class ObatAlkesForm extends \yii\base\Model
{
    public $obatalkes_nama;
    public $obatalkes_kode;

    public function rules()
    {
        return [
            [['obatalkes_nama', 'obatalkes_kode'], 'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'obatalkes_nama' => Yii::t('fe', 'Nama obat alkes'),
            'obatalkes_kode' => Yii::t('fe', 'Kode obat alkes'),
        ];
    }


}
