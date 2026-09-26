<?php

namespace Doco\kasir\models;

use Yii;

class PelayananKasirForm extends \yii\base\Model
{
    
    public $tanggal_pembayaran;
    public $tanggal_tindakan;
    public $jenis_pelayanan;
    public $instalasi_id;
    public $ruangan_id;
    public $kelas_pelayanan;
    public $dokter_pj;
    public $tindakan_pelayanan_id;
    public $qty;
    public $is_cyto;
    public $uang_diterima;

    public $ruangan_pelakhir_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $nama_pasien;
    public $jumlah_uangmuka;
    public $status_pasien;
    public $pengguna_uang_muka;
    public $total_tagihan;
    public $subsidi_asuransi;
    public $tagihan_pasien;
    public $pembulatan;
    public $biaya_administrasi;
    public $uang_kembalian;

    public $detail_tagihan;

    public function rules()
    {
        return [
            [[
                'uang_kembalian',
                'biaya_administrasi',
                'pembulatan',
                'subsidi_asuransi',
                'tagihan_pasien',
                'total_tagihan',
                'pengguna_uang_muka',
                'tanggal_tindakan',
                'tanggal_pembayaran',
                'jenis_pelayanan',
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan',
                'dokter_pj',
                'tindakan_pelayanan_id',
                'qty',
                'is_cyto',
                'ruangan_pelakhir_id',
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasien_id',
                'nama_pasien',
                'jumlah_uangmuka',
                'status_pasien',
                'detail_tagihan',
                'uang_diterima',
            ],'safe'],
            [[
                'tanggal_tindakan',
                'jenis_pelayanan',
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan',
                'dokter_pj',
                'tindakan_pelayanan_id',
                'qty',
            ],'required'],
            ['qty', 'integer', 'min' => 1],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dokter_pj' => 'Dokter Penanggung jawab',
            'instalasi_id' => 'Instalasi',
            'ruangan_id' => 'Ruangan',
            'kelas_pelayanan' => 'Kelas Pelayanan',
            'tindakan_pelayanan_id' => 'Tindakan / Obat Alkes'
        ];
    }
}