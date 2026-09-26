<?php

/**
 * @author: [Fajar Supriad][fajar.supriadi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class KeluargaPasienForm extends \Doco\components\DocoBaseModel
{
    public $keluarga_nama;
    public $keluarga_jk;
    public $keluarga_hubungan;
    public $keluarga_alamat;
    public $keluarga_no_telepon;
    public $keluarga_namadepan;
    public $keluarga_propinsi_id;
    public $keluarga_kabupaten_id;
    public $keluarga_kecamatan_id;
    public $keluarga_kelurahan_id;
    public $keluarga_pekerjaan_id;
    public $keluarga_rt;
    public $keluarga_rw;
    public $pasien_id;
    public $alamatdepan;

    protected $xssProtected = [
        'keluarga_nama', 
        'keluarga_jk',
    ];

    public function rules()
    {
         return [
            [[
                'keluarga_nama', 
                'keluarga_jk',
                'keluarga_namadepan',
            ], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [[
                'keluarga_jk',
                'keluarga_hubungan',
                'keluarga_alamat',
                'keluarga_no_telepon',
                'keluarga_namadepan',
                'keluarga_propinsi_id',
                'keluarga_kabupaten_id',
                'keluarga_kecamatan_id',
                'keluarga_kelurahan_id',
                'keluarga_pekerjaan_id',
                'keluarga_rt',
                'keluarga_rw',
                'pasien_id',
                'alamatdepan',
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'keluarga_nama' => 'Nama',
            'keluarga_jk' => 'Jenis kelamin',
            'keluarga_hubungan' => 'Hubungan',
            'keluarga_alamat'=> 'Alamat',
            'keluarga_no_telepon'=> 'No telepon',
            'is_pj'=> 'Penangung Jawab',
            'keluarga_namadepan' => 'Nama Depan',
            'keluarga_propinsi_id' => 'Propinsi',
            'keluarga_kabupaten_id' => 'Kabupaten/Kota',
            'keluarga_kecamatan_id'=> 'Kecamatan',
            'keluarga_kelurahan_id'=> 'Kelurahan',
            'keluarga_pekerjaan_id'=> 'Pekerjaan',
            'keluarga_rt'=> 'RT',
            'keluarga_rw'=> 'RW',
            'alamatdepan'=> 'Alamat Depan',
        ];
    }


}
