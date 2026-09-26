<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-07 13:28:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-07 17:11:15
 */

namespace app\modules\ranap\models;

use Yii;

class KalaSatuForm extends \yii\base\Model
{
    public $k1_gariswaspada;
    public $k1_masalah;
    public $k1_pelaksanaanmasalah;
    public $k1_hasil;
    public $persalinan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;

    public function rules()
    {
        return [
            [['k1_gariswaspada'], 'required'],
            [['k1_masalah', 'k1_pelaksanaanmasalah', 'k1_hasil', 'k1_gariswaspada', 'persalinan_id', 'pendaftaran_id', 'pasienadmisi_id'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'k1_gariswaspada' => \Yii::t('fe', 'Partogram Lewat Garis Waspada'),
            'k1_masalah' => \Yii::t('fe', 'Masalah Lain'),
            'k1_pelaksanaanmasalah' => \Yii::t('fe', 'Penatalaksanaan Masalah'),
            'k1_hasil' => \Yii::t('fe', 'Hasil'),
        ];
    }
}
