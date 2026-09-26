<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class PjPasien extends \Doco\components\DocoBaseModel
{
    public $pj_pengantar;
    public $pj_nama;
    public $pj_jk;
    public $pj_jenis_identitas;
    public $pj_no_identitas;
    public $pj_hubungan;
    public $pj_tempat_lahir;
    public $pj_tanggal_lahir;
    public $pj_umur;
    public $pj_alamat;
    public $pj_no_telepon;
    public $pj_namadepan;
    public $pj_propinsi_id;
    public $pj_kabupaten_id;
    public $pj_kecamatan_id;
    public $pj_kelurahan_id;
    public $pj_pekerjaan_id;
    public $pj_rt;
    public $pj_rw;

    protected $xssProtected = [
        'pj_pengantar',
        'pj_nama',
        'pj_jk',
        'pj_jenis_identitas',
        'pj_no_identitas',
        'pj_hubungan',
        'pj_tempat_lahir',
        'pj_tanggal_lahir',
        'pj_umur',
        'pj_alamat',
        'pj_no_telepon',
        'pj_namadepan',
        'pj_propinsi_id',
        'pj_kabupaten_id',
        'pj_kecamatan_id',
        'pj_kelurahan_id',
        'pj_pekerjaan_id',
        'pj_rt',
        'pj_rw',
    ];

    public function rules()
    {
         return [
            [[
                'pj_pengantar',
                'pj_nama',
                'pj_jk'
            ], 'required', 'on' => 'default', 'message'=>'{attribute} Tidak boleh kosong'],
            [[
                'pj_pengantar',
                'pj_nama',
                'pj_jk'
            ], 'safe', 'on' => 'pendaftaran-igd', 'message'=>'{attribute} Tidak boleh kosong'],
            [[
                'pj_pengantar',
                'pj_no_identitas',
                'pj_hubungan',
                'pj_nama',
            ], 'string', 'max' => 50],
            [[
                'pj_jenis_identitas',
                'pj_tempat_lahir',
                'pj_jk',
            ], 'string', 'max' => 20],
            [[
                'pj_no_telepon',
            ], 'string', 'max' => 15],
            [[
                'pj_pengantar',
                'pj_nama',
                'pj_jk',
                'pj_jenis_identitas',
                'pj_no_identitas',
                'pj_hubungan',
                'pj_tempat_lahir',
                'pj_tanggal_lahir',
                'pj_umur',
                'pj_alamat',
                'pj_no_telepon',
                'pj_namadepan',
                'pj_propinsi_id',
                'pj_kabupaten_id',
                'pj_kecamatan_id',
                'pj_kelurahan_id',
                'pj_pekerjaan_id',
                'pj_rt',
                'pj_rw',
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pj_pengantar' =>  'Pengantar',
            'pj_nama' =>  'Nama',
            'pj_jk' =>  'Jenis kelamin',
            'pj_jenis_identitas' =>  'Jenis identitas',
            'pj_no_identitas' =>  'No identitas',
            'pj_hubungan' =>  'Hubungan',
            'pj_tempat_lahir' =>  'Tempat lahir',
            'pj_tanggal_lahir'=>  'Tanggal lahir',
            'pj_umur'=>  'Umur',
            'pj_alamat'=>  'Alamat',
            'pj_no_telepon'=>  'No telepon',
            'is_pj'=>  'Penangung Jawab',
        ];
    }


}
