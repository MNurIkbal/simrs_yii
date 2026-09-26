<?php

namespace app\modules\v1\models;

use Yii;


class TmpInfoTagihanPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanpasien_r';
    }

    /**
     * {@inheritdoc}
     */
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
                'value', 
                'penyulit', 
                'pelayanan_id', 
                'harga_origin', 
                'cyto_origin', 
                'penyulit_origin', 
                'id', 
                'subPenjamin', 
                'dijamin_subpayer',
                'plafon_payer' ,
                'plafon_subpayer', 
            ],'safe'],
            [['penyulit_origin', 'cyto_origin', 'harga_origin'], 'default', 'value'=> 0],
            [['pasienmasukpenunjang_id', 'pendaftaran_id'], 'default', 'value'=> null],
        ];
    }

    /**
     * {@inheritdoc}
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
            'value' => 'value',
        ];
    }
}
