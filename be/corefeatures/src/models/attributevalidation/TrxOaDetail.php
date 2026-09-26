<?php

namespace SirsCore\models\attributevalidation;

use Yii;

class TrxOaDetail extends \yii\base\Model
{
    public $is_ditagihkan;
    public $obatalkes_id;
    public $tipepaket_id;
    public $ruangan_id;
    public $carabayar_id;
    public $pegawai_id;
    public $daftartindakan_id;
    public $tindakanpelayanan_id;
    public $satuankecil_id;
    public $pendaftaran_id;
    public $pasien_id;
    public $penjamin_id;
    public $kelaspelayanan_id;
    public $pasienmasukpenunjang_id;
    public $pasienadmisi_id;
    public $obatsudahbayar_id;
    public $penjualanresep_id;
    public $tglpelayanan;
    public $rke;
    public $qty_oa;
    public $hargasatuan_oa;
    public $signa_oa;
    public $harganetto_oa;
    public $hargajual_oa;
    public $resepturdetail_id;
    public $additional_data;
    public $created_by;
    public $perawat1_id;
    public $perawat2_id;
    public $instruksitindakanbmhp_id;
    public $implementasi_id;
    public $is_dilakukan;

    public function rules()
    {
        return [
            [[
                'obatalkes_id','is_ditagihkan'
            ], 'required'],
            [['obatalkes_id','is_ditagihkan'],'safe'],
            [[
                'tipepaket_id',
                'ruangan_id',
                'carabayar_id',
                'pegawai_id',
                'daftartindakan_id',
                'tindakanpelayanan_id',
                'satuankecil_id',
                'pendaftaran_id',
                'obatalkes_id',
                'pasien_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'pasienmasukpenunjang_id',
                'pasienadmisi_id',
                'obatsudahbayar_id',
                'penjualanresep_id',
                'tglpelayanan',
                'rke',
                'qty_oa',
                'hargasatuan_oa',
                'signa_oa',
                'harganetto_oa',
                'hargajual_oa',
                'resepturdetail_id',
                'additional_data',
                'created_by',
                'perawat1_id',
                'perawat2_id',
                'instruksitindakanbmhp_id',
                'implementasi_id',
                'is_dilakukan'
            ],'filter','filter'=>'\yii\helpers\HtmlPurifier::process']
        ];
    }
}