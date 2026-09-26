<?php

/**
 * @author yaya
 */

namespace Doco\penjaminasuransi\models;

class TransaksiAlokasiForm extends \yii\base\Model
{
    public $no_pengajuan;
    public $tanggal_pembayaran;
    public $no_pembayaran;
    public $jumlah_pembayaran;
    public $total_pengajuan;
    public $total_terbayar;
    public $sisa_piutang;
    public $catatan;

    public $detail_pembayaran;
    public $total_alokasi;

    public function rules()
    {
        return [
            [[
                'no_pengajuan',
                'tanggal_pembayaran',
                'no_pembayaran',
                'jumlah_pembayaran',
                'total_pengajuan',
                'total_terbayar',
                'sisa_piutang',
                'catatan',
                'detail_pembayaran',
                'total_alokasi',
            ],'safe'],
            [[
                'no_pengajuan',
                'tanggal_pembayaran',
                'no_pembayaran',
                'jumlah_pembayaran',
            ],'required'],
            [['jumlah_pembayaran'],'checkPembayaran']
        ];
    }

    public function checkPembayaran($attributes, $paramas)
    {
        if ($this->jumlah_pembayaran != $this->total_alokasi) {
            $this->addError('jumlah_pembayaran','Jumlah pembayaran harus sama dengan total alokasi pembayaran');
        }
    }

    public function attributeLabels()
    {
        return [
            'no_pengajuan' => 'No. Pengajuan',
            'tanggal_pembayaran' => 'Tanggal Pembayaran',
            'no_pembayaran' => 'No Pembayaran',
            'jumlah_pembayaran' => 'Jumlah Pembayaran',
            'total_pengajuan' => 'Total Pengajuan',
            'total_terbayar' => 'Total Telah Bayar',
            'sisa_piutang' => 'Sisa Piutang',
            'catatan' => 'Catatan',
            'detail_pembayaran' => 'Alokasi Pembayaran',
        ];
    }

}