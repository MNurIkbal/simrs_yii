<?php

namespace app\components\models;

use Yii;

class ObatForm extends \yii\base\Model
{
    public $obatalkes_id;
    public $qty;
    public $is_tagihkan;
    public $petugas_satu;
    public $petugas_dua;
    public $jumlah_tarif;
    public $stok;
    public $pemeriksaan_id;
    public $jenis;
    public $depo_id;
    public $daftartindakan_id;

    public function rules()
    {
        return [
            [['daftartindakan_id','qty', 'obatalkes_id', 'depo_id'], 'required'],
            [['qty', 'petugas_satu', 'obatalkes_id', 'depo_id', 'daftartindakan_id'], 'integer', 'min' => 1],
            [["is_tagihkan"], 'default', "value" => 0],
            // [['qty'], 'checkQty'],
            [[
                'obatalkes_id',
                'qty',
                'is_tagihkan',
                'petugas_satu',
                'petugas_dua',
                'stok',
                'pemeriksaan_id',
                'jenis',
                'depo_id',
                'daftartindakan_id'
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
            'obatalkes_id' => \Yii::t('fe', 'Obat/Alkes'),
            'qty' => \Yii::t('fe', 'Jumlah'),
            'is_tagihkan' => \Yii::t('fe', 'Tagihkan ke Pasien'),
            'petugas_satu' => \Yii::t('fe', 'Petugas 1'),
            'petugas_dua' => \Yii::t('fe', 'Petugas 2'),
            'jumlah_tarif' => \Yii::t('fe', 'Jumlah Tarif'),
            'pemeriksaan_id' => \Yii::t('fe', 'Nama Pemeriksaan'),
            'depo_id' => \Yii::t('fe', 'Depo'),
            'daftartindakan_id' => \Yii::t('fe', 'Nama Tindakan'),
        ];
    }

}
