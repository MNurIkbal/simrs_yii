<?php

/**
 * @author: [Budi][budi@docotel.com
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class PemberianPiutangPayload extends \Doco\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $tgl_pemberianpiutang;
    public $pegawai_id;
    public $total_piutang;
    public $no_pendaftaran;
    public $catatan;
    public $total_sisapiutang;
    public $no_pemberianpiutang;
    public $penjualanresep_id;
    public $pegawaimengetahui_id;
    public function rules()
    {
         return [
            [[

                'pendaftaran_id', 
                'tgl_pemberianpiutang',
                'pegawai_id',
                'total_piutang',
                'no_pendaftaran',
                'catatan',
                'total_sisapiutang',
                'no_pemberianpiutang',
                'penjualanresep_id',
                'pegawaimengetahui_id',
                'is_reseptur',
            ], 'safe'],
            [[
                'pendaftaran_id',
                'tgl_pemberianpiutang',
                'pegawai_id',
                'total_piutang',
            ], 'required', 'on' => 'default'],
            [[
                'penjualanresep_id',
                'tgl_pemberianpiutang',
                'pegawai_id',
                'total_piutang',
            ], 'required', 'on' => 'reseptur'],
            [['penjualanresep_id'], 'validReseptur'],
            ['tgl_pemberianpiutang', 'datetime', 'format' => 'php:Y-m-d'],
            [[
                'pendaftaran_id',
                'penjualanresep_id',
                'pegawaimengetahui_id',
                'pegawai_id',
            ], 'integer', 'min' => 0]
        ];
    }

    public function validReseptur($attribute_name, $params)
    {
        if (!empty($this->penjualanresep_id) && empty($this->pegawaimengetahui_id) && empty($this->pendaftaran_id)) {
            $this->addError("pegawaimengetahui_id", "Nama Karyawan tidak boleh kosong");
        }
    }

    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Di Approve Oleh',
            'total_bayarpiutang' => 'Sudah Bayar',
            'total_sisapiutang' => 'Balance Piutang',
            'total_piutang' => 'Jumlah Piutang',
            'pegawaimengetahui_id' => 'Nama Karyawan',
            'penjualanresep_id' => 'Penjualan Resep',
            'tgl_pemberianpiutang' => 'Tanggal Pemberian',
        ];
    }
}
