<?php

namespace Doco\radiologi\models;

use Yii;

class OrderForm extends \yii\base\Model
{
    public $unit_tujuan;
    public $tgl_kirimpasien;
    public $tgl_permintaan;
    public $dokter_perujuk;
    public $catatan_dokter;

    public function attributeLabels()
    {
        return [
            'unit_tujuan' => \Yii::t('fe', 'Unit Tujuan'),
            'tgl_kirimpasien' => \Yii::t('fe', 'Tanggal Kirim Pasien'),
            'tgl_permintaan' => \Yii::t('fe', 'Tanggal Permintaan'),
            'dokter_perujuk' => \Yii::t('fe', 'Dokter Perujuk'),
            'catatan_dokter' => \Yii::t('fe', 'Catatan Dokter')
        ];
    }
}