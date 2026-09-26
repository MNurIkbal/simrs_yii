<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-14 11:26:14
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-05-12 03:18:15
 */


namespace Doco\apotek\models;

use Yii;

class PenerimaanObatForm extends \yii\base\Model
{
    public $tglterima;
    public $tglmutasioa;
    public $instalasi;
    public $nomutasioa;
    public $nopemesanan;
    public $ruangan;
    public $pegawai_mengetahui;
    public $pegawai_menyetujui;
    public $status_mutasi;
    public $statusmutasi;
    public $pengirim;
    public $nama_pegawai_mengetahui;

    public $totalharganetto;
    public $totalhargajual;
    public $ruangan_penerima;
    public $ruangan_asal;
    public $data_obat;


    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['obatalkes_id'], 'required'],
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'tglterima' => 'Tanggal Terima',
            'tglmutasioa' => 'Tanggal Kirim',
            'instalasi' => 'Instalasi Pengirim',
            'nomutasioa' => 'No Pengiriman',
            'nopemesanan' => 'No Pemesanan',
            'ruangan' => 'Ruangan Pengirim',
            'statusmutasi' => 'Status',
            'pegawai_mengetahui' => 'Penerima',
            'pegawai_menyetujui' => 'Pegawai Menyetujui',
            'pengirim' => 'Pengirim'
        ];
    }

    public function loadFromMutasi($data)
    {
        $this->tglterima = $data['tgl_terima'];
        $this->tglmutasioa = $data['tglmutasioa'];
        $this->instalasi = $data['instalasi_asal'];
        $this->ruangan = $data['ruangan_asal'];
        $this->nomutasioa = $data['nomutasioa'];
        $this->nopemesanan = $data['nopemesanan'];
        $this->status_mutasi = $data['status_mutasi'];
        $this->statusmutasi = $data['statusmutasi'];
        $this->pengirim = $data['pegawai_mutasi'];
        $this->pegawai_mengetahui = $data['id_pegawai_mengetahui'];
        $this->pegawai_menyetujui = $data['id_pegawai_penerima'];
        $this->nama_pegawai_mengetahui = $data['nama_pegawai_mengetahui'];
    }
}
