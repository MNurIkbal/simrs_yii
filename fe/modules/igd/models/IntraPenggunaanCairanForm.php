<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 17:00:04
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-13 17:02:11
 */

namespace Doco\igd\models;

class IntraPenggunaanCairanForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $kegiatan;
    public $cairan_masuk;
    public $cairan_keluar;
    public $keterangan;

    public function rules()
    {
        return [
            [['kegiatan'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id','cairan_masuk','cairan_keluar', 'keterangan'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'kegiatan'=>\Yii::t('fe', 'Kegiatan'),
            'cairan_masuk'=>\Yii::t('fe', 'Cairan masuk'),
            'cairan_keluar'=>\Yii::t('fe', 'Cairan keluar'),
            'keterangan'=>\Yii::t('fe', 'Keterangan'),
        ];
    }
}