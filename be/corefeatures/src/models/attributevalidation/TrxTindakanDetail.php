<?php

namespace SirsCore\models\attributevalidation;

use Yii;

class TrxTindakanDetail extends \yii\base\Model
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
                'daftartindakan_id','cyto_tindakan'
            ], 'required'],
            [['daftartindakan_id','cyto_tindakan'],'safe'],
            [[
                'tipepaket_id',
                
            ],'filter','filter'=>'\yii\helpers\HtmlPurifier::process']
        ];
    }
}