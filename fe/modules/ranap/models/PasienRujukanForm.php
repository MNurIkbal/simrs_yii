<?php

namespace app\modules\ranap\models;

use Yii;

class PasienRujukanForm extends \yii\base\Model
{

    public $diagnosa_awal; 
    public $diagnosa_keluar;
    
    public $keluhan_utama;
    public $r_penyakitsekarang;
    public $r_penyakitdahulu;
    public $kesadaran;
    public $saturasi_o2;
    public $tensi;
    public $suhu;
    public $nadi;
    public $pernafasan;

    public $pemeriksaan_penunjang;
    public $tindakan_medis;
    public $pemberian_terapi;

    public $pegawai_nama;

    public function rules() 
    {
        return [
            [['diagnosa_awal', 'diagnosa_keluar', 'keluhan_utama', 'r_penyakitsekarang','r_penyakitdahulu','kesadaran','saturasi_o2','tensi','suhu','nadi','pernafasan','pemeriksaan_penunjang',
            'tindakan_medis','pemberian_terapi','pegawai_nama'], 'default', 'value' => null],
            [['diagnosa_awal', 'diagnosa_keluar', 'keluhan_utama', 'r_penyakitsekarang','r_penyakitdahulu','kesadaran','saturasi_o2','tensi','suhu','nadi','pernafasan','pemeriksaan_penunjang',
            'tindakan_medis','pemberian_terapi','pegawai_nama'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'diagnosa_awal'  => 'Diagnosa Masuk RS',
            'diagnosa_keluar' => 'Diagnosa Keluar RS',
            'keluhan_utama' => 'Keluhan Utama',
            'r_penyakitsekarang' => 'Riwayat Penyakit Sekarang',
            'r_penyakitdahulu' => 'Riwayat Penyakit Dahulu',
            'kesadaran' => 'Kesadaran',
            'saturasi_o2' => 'SpO@',
            'tensi' => 'Tensi',
            'suhu' => 'Suhu',
            'nadi' => 'Nadi',
            'pernafasan' => 'Pernafasan',
            'pemeriksaan_penunjang' => 'pemeriksaan penunjang',
            'tindakan_medis' => 'tindakan medis',
            'pemberian_terapi' => 'pemberian terapi',
            'pegawai_nama' => 'pegawai nama',
        ];
    }
}
