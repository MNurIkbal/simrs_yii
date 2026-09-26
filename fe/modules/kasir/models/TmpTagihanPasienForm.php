<?php

/**
* @author dede
*/

namespace Doco\kasir\models;

use Yii;

class TmpTagihanPasienForm extends \yii\base\Model
{

    public $pendaftaran_id;
    public $pasienmasukpenunjang_id;
    public $kelompok;
    public $status;
    public $tipe_pasien;
    public $checkPenjamin;
    public $cyto;
    public $defaultPenjamin;
    public $dijamin;
    public $harga;
    public $instalasi;
    public $isPenjamin;
    public $is_obat;
    public $kelompoktindakan_nama;
    public $keterangan;
    public $nominal_diskon;
    public $penjamin;
    public $persen_diskon;
    public $qty;
    public $subtotal;
    public $subtotal_origin;
    public $totalDibayar;
    public $tindakan_obat_id;

    public function rules()
    {
        return [
            [[
                'pendaftaran_id',
                'pasienmasukpenunjang_id',
                'kelompok',
                'status', 
                'tipe_pasien', 
                'checkPenjamin',
                'cyto', 
                'defaultPenjamin',
                'dijamin',
                'harga', 
                'instalasi', 
                'isPenjamin',
                'is_obat', 
                'kelompoktindakan_nama', 
                'keterangan', 
                'nominal_diskon', 
                'penjamin',
                'persen_diskon', 
                'qty',
                'subtotal',
                'subtotal_origin', 
                'tanggal', 
                'tindakan', 
                'tindakan_obat_id', 
                'totalDibayar', 
            ],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienmasukpenunjang_id' => 'Pasien masuk penunjang ID',
            'kelompok' => 'Kelompok ',
            'status' => 'status',
            'tipe_pasien' => 'tipe pasien',
            'checkPenjamin' => "Check penjamin",
            'cyto' => 'Cito',
            'defaultPenjamin' => 'Penjamin',
            'dijamin' => 'Dijamin',
            'harga' => 'Harga',
            'instalasi' => 'Instalasi',
            'isPenjamin' => 'Is Penjamin',
            'is_obat' => 'Is Obat',
            'kelompoktindakan_nama' => 'Kelompok Tindakan Nama',
            'keterangan' => 'Keterangan',
            'nominal_diskon' => 'Nominal Diskon',
            'penjamin' => 'Penjamin', 
            'persen_diskon' => 'Persen Diskon',
            'qty' => 'QTY',
            'subtotal' => 'Subtotal',
            'subtotal_origin' => 'Subtotal Origin',
            'tanggal' => "Tanggal",
            'tindakan' => "Tindakan",
            'tindakan_obat_id' => 'Tindakan Obat Id', 
            'totalDibayar' => 'Total Dibayar',
        ];
    }
}