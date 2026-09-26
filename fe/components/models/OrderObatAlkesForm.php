<?php

namespace app\components\models;

use Yii;

class OrderObatAlkesForm extends \yii\base\Model
{
    public $jenis_pemakaian_id;
    public $tindakan_id;
    public $obatalkes_id;
    public $qty;
    public $is_tagihkan;
    public $petugas_satu;
    public $petugas_dua;
    public $jumlah_tarif;
    public $stok;
    public $pemeriksaan_id;
    public $qty_tindakan;
    public $jenis;

    public function rules()
    {
        return [
            [['petugas_satu','pemeriksaan_id'], 'required'],
            [['qty_tindakan', 'tindakan_id'], 'required', 'on' => 'tindakan'],
            [['qty', 'obatalkes_id'], 'required', 'on' => 'obat'],
            [['qty', 'qty_tindakan'], 'integer', 'min' => 1],
            [["is_tagihkan"], 'default', "value" => 0],
            [['qty'], 'checkQty'],
            [[
                'jenis_pemakaian_id',
                'tindakan_id',
                'obatalkes_id',
                'qty',
                'is_tagihkan',
                'petugas_satu',
                'petugas_dua',
                'stok',
                'pemeriksaan_id',
                'qty_tindakan',
                'jenis'
            ], 'safe']
        ];
    }


    public function checkQty($params, $attributes)
    {
        if ($this->stok < $this->qty) {
            $this->addError("qty","Stok tidak mencukupi");
        }
    }

    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'tindakan_id' => \Yii::t('fe', 'Nama Tindakan'),
            'obatalkes_id' => \Yii::t('fe', 'Pemakaian Obat/Alkes'),
            'qty' => \Yii::t('fe', 'Jumlah'),
            'is_tagihkan' => \Yii::t('fe', 'Tagihkan ke Pasien'),
            'petugas_satu' => \Yii::t('fe', 'Petugas 1'),
            'petugas_dua' => \Yii::t('fe', 'Petugas 2'),
            'jumlah_tarif' => \Yii::t('fe', 'Jumlah Tarif'),
            'pemeriksaan_id' => \Yii::t('fe', 'Nama Pemeriksaan'),
            'qty_tindakan' => \Yii::t('fe', 'Jumlah'),
        ];
    }

}
