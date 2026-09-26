<?php

namespace app\modules\ambulan\models;

use Yii;

class FormProses extends \yii\base\Model
{
     const SCENARIO_LUAR = 'luar';
     const SCENARIO_RS = 'rs';
     public $pendaftaran_id;
     public $pasien_id;

     public $tgl_pemakaiandari;
     public $tgl_pemakaiansampai;
     public $durasi_pemakaian;
     public $pelayanan_ambulan;
     public $km_awal;
     public $estimasi_jarak;
     public $tgl_pemakaian;
     public $jenis;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'tgl_pemakaiandari',
                'tgl_pemakaiansampai',
                'pendaftaran_id',
                'pasien_id',
                'pelayanan_ambulan',
                'km_awal',
            ],'safe'],
            [[
                'pelayanan_ambulan',
                'km_awal',
                'tgl_pemakaian',
            ], 'required', 'on' => self::SCENARIO_LUAR,'message'=>'{attribute} Tidak boleh kosong'],
            [[
                // 'tgl_pemakaiandari',
                // 'tgl_pemakaiansampai',
                'pendaftaran_id',
                'pasien_id',
                // 'durasi_pemakaian',
                'pelayanan_ambulan',
                'km_awal',
                'tgl_pemakaian',
                'estimasi_jarak',
            ], 'required', 'on' => self::SCENARIO_RS,'message'=>'{attribute} Tidak boleh kosong'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_pemakaiandari' => 'Tanggal pemakaian',
            'tgl_pemakaiansampai' => 'Tanggal pemakaian',
            'durasi_pemakaian' => 'Durasi pemakaian',
            'pelayanan_ambulan' => 'Pelayanan Ambulan',
            'estimasi_jarak' => 'Estimasi Jarak (km)',
            'km_awal' => 'KM Awal',
        ];
    }
}
