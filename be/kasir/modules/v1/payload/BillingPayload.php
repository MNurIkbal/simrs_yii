<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class BillingPayload extends \Doco\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $penjualanresep_id;
    public $condition;
    public $detail_tagihan;
    public $data_penjamin;
    public $jumlah_uangmuka;
    public $data_metode_pembayaran;
    public $data_diskon;
    public $total_diskon;
    public $catatan;
    public $total_dibayar;
    public $status_pasien;
    public $nama_pasien;
    public $type_antrian;
    public $instalasi_id;
    public $adm_asuransi;
    public $plafon_payer;
    public $plafon_subpayer;

    protected $xssProtected = [
        'catatan',
    ];

    public function rules()
    {
         return [
            [[
                'pendaftaran_id', 
                'pasienadmisi_id',
                'penjualanresep_id',
                'penjualanresep_id',
                'condition',
                'detail_tagihan',
                'data_penjamin',
                'jumlah_uangmuka',
                'data_metode_pembayaran',
                'data_diskon',
                'total_diskon',
                'catatan',
                'total_dibayar',
                'status_pasien',
                'nama_pasien',
                'type_antrian',
                'instalasi_id',
                'adm_asuransi',
                'plafon_payer',
                'plafon_subpayer',
            ], 'safe']
        ];
    }
}