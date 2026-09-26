<?php

namespace app\components\models;

use Yii;

class TindakanForm extends \yii\base\Model
{
    public $tindakan_id;
    public $is_tagihkan;
    public $petugas_satu;
    public $petugas_dua;
    public $jumlah_tarif;
    public $pemeriksaan_id;
    public $qty;
    public $jenis;
    public $tanggal_tindakan;

    public function rules()
    {
        return [
            [['petugas_satu', 'qty', 'tindakan_id'], 'required'],
            [['qty', 'petugas_satu', 'tindakan_id'], 'integer', 'min' => 1],
            [["is_tagihkan"], 'default', "value" => 0],
            [[
                'tindakan_id',
                'is_tagihkan',
                'petugas_satu',
                'petugas_dua',
                'pemeriksaan_id',
                'qty',
                'jenis',
                'tanggal_tindakan'
            ], 'safe']
        ];
    }

    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'tindakan_id' => \Yii::t('fe', 'Nama Tindakan'),
            'is_tagihkan' => \Yii::t('fe', 'Tagihkan ke Pasien'),
            'petugas_satu' => \Yii::t('fe', 'Petugas 1'),
            'petugas_dua' => \Yii::t('fe', 'Petugas 2'),
            'jumlah_tarif' => \Yii::t('fe', 'Jumlah Tarif'),
            'pemeriksaan_id' => \Yii::t('fe', 'Nama Pemeriksaan'),
            'qty' => \Yii::t('fe', 'Jumlah'),
        ];
    }

}
