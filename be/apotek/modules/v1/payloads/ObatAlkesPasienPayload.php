<?php

namespace app\modules\v1\payloads;

use Yii;
use Doco\components\DocoBaseModel;

class ObatAlkesPasienPayload extends DocoBaseModel
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
    public $det;
    public $det_konversi;
    public $qty_medis;
    public $det_medis;
    public $nama_racikan;
    public $qty_racikan;
    public $satuan_racikan_id;
    public $is_kronis;

    public function rules()
    {
        return [
            [[
                'obatalkes_id','is_ditagihkan'
            ], 'required'],
            [['obatalkes_id','is_ditagihkan','is_kronis'],'safe'],
            [[
                'tglpelayanan',
                'nama_racikan',
            ],'filter','filter'=>'\yii\helpers\HtmlPurifier::process']
        ];
    }
}