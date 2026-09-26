<?php

namespace app\modules\v1\payload;

use Yii;


class MultiCarabayar extends \yii\base\Model
{
    public $add_carabayar_id_1;
    public $add_penjamin_id_1;
    public $add_asalrujukan_id_1;
    public $add_carabayar_id_2;
    public $add_penjamin_id_2;
    public $add_asalrujukan_id_2;
    public $additional;
    public $add_no_asuransi_1;
    public $add_no_asuransi_2;
    public $add_carabayargroup_1;
    public $add_carabayargroup_2;
    public $flagBpjs;
    public $carabayar_utama;
    public $carabayargroup_utama;
    public $add_nokartuasuransi_1;
    public $add_namapemilikasuransi_1;
    public $add_nomorpokokperusahaan_1;
    public $add_kelastanggungan_id_1;
    public $add_namaperusahaan_1;
    public $add_tgl_konfirmasi_1;
    public $add_status_konfirmasi_1;
    public $add_asuransipasien_id_1;
    public $add_nokartuasuransi_2;
    public $add_namapemilikasuransi_2;
    public $add_nomorpokokperusahaan_2;
    public $add_kelastanggungan_id_2;
    public $add_namaperusahaan_2;
    public $add_tgl_konfirmasi_2;
    public $add_status_konfirmasi_2;
    public $add_asuransipasien_id_2;
    public $is_add_payer;
    public $isMultipayer;
    public $add_penjamingrade_id_1;
    public $add_penjamingrade_id_2;

    public function rules()
    {
        return [
            [[
                'add_carabayar_id_1',
                'add_penjamin_id_1',
                'add_asalrujukan_id_1',
                'add_carabayar_id_2',
                'add_penjamin_id_2',
                'add_asalrujukan_id_2',
                'additional',
                'add_no_asuransi_1',
                'add_no_asuransi_2',
                'add_carabayargroup_1',
                'add_carabayargroup_2',
                'flagBpjs',
                'carabayar_utama',
                'carabayargroup_utama',
                'add_nomorpokokperusahaan_1', 
                'add_kelastanggungan_id_1',
                'add_namaperusahaan_1',
                'add_tgl_konfirmasi_1',
                'add_status_konfirmasi_1',
                'add_nokartuasuransi_1',
                'add_asuransipasien_id_1'.
                'add_nomorpokokperusahaan_2', 
                'add_kelastanggungan_id_2',
                'add_namaperusahaan_2',
                'add_tgl_konfirmasi_2',
                'add_status_konfirmasi_2',
                'add_nokartuasuransi_2',
                'add_asuransipasien_id_2',
                'is_add_payer',
                'isMultipayer',
                'add_penjamingrade_id_1',
                'add_penjamingrade_id_2'
            ],'safe'],
            [[
                'add_carabayar_id_1',
                'add_penjamin_id_1',
            ],'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'add_carabayar_id_1' =>'Cara bayar',
            'add_penjamin_id_1' =>'Penjamin',
            'add_asalrujukan_id_1' =>'Asal Rujukan',
            'add_carabayar_id_2' =>'Cara bayar',
            'add_penjamin_id_2' =>'Penjamin',
            'add_asalrujukan_id_2' => 'Asal Rujukan',
            'additional' =>'Additional',
            'add_no_asuransi_1' =>'No Asuransi',
            'add_no_asuransi_2' =>'No Asuransi',
            'add_nokartuasuransi_1' =>  'Nomor asuransi',
            'add_namapemilikasuransi_1' =>  'Nama pemilik',
            'add_nomorpokokperusahaan_1' =>  'Nomor pokok perusahaan',
            'add_kelastanggungan_id_1' =>  'Kelas tanggungan',
            'add_namaperusahaan_1' =>  'Nama perusahaan',
            'add_tgl_konfirmasi_1' =>  'Tanggal Konfirmasi',
            'add_status_konfirmasi_1' =>  'Telah konfirmasi',
            'add_asuransipasien_id_1'=> 'Pasien',
            'add_nokartuasuransi_2' =>  'Nomor asuransi',
            'add_namapemilikasuransi_2' =>  'Nama pemilik',
            'add_nomorpokokperusahaan_2' =>  'Nomor pokok perusahaan',
            'add_kelastanggungan_id_2' =>  'Kelas tanggungan',
            'add_namaperusahaan_2' =>  'Nama perusahaan',
            'add_tgl_konfirmasi_2' =>  'Tanggal Konfirmasi',
            'add_status_konfirmasi_2' =>  'Telah konfirmasi',
            'add_asuransipasien_id_2'=> 'Pasien',
            'add_penjamingrade_id_1' => 'Penjamin Grade',
            'add_penjamingrade_id_2' => 'Penjamin Grade'
        ];
    }

}
