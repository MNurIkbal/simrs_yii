<?php

namespace app\modules\master\models;

use Yii;

class KonfigantrianForm extends \yii\base\Model
{
    public $fungsiantrian_id_config;
    public $ruangan_id;
    public $jenisantrian_id;
    public $jenisantrian_nama;
    public $groupcarabayar_id;
    public $fungsi_antrian_id;
    public $klasifikasipasien_id;
    public $kode_antrian;
    public $instalasi_id;
    public $is_active;
    public $pegawai_id;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'fungsiantrian_id_config',
                    'ruangan_id',
                    'jenisantrian_id',
                    'jenisantrian_nama',
                    'fungsi_antrian_id',
                    'groupcarabayar_id',
                    'klasifikasipasien_id',
                    'jenisantrian_id',
                    'kode_antrian',
                    'instalasi_id',
                    'ruangan_id',
                    'is_active',
                ],'safe'],
                [
                    [
                        'jenisantrian_id',
                        // 'fungsi_antrian_id',
                        // 'groupcarabayar_id',
                        'kode_antrian',
                        'is_active',
                    ],'required'
                ],
                [
                    [
                        'jenisantrian_id',
                        // 'fungsi_antrian_id',
                        'groupcarabayar_id',
                        'kode_antrian',
                        'instalasi_id',
                        'ruangan_id',
                        'is_active',
                    ],'required', 'on' => 'instalasi'
                ],
                [
                    [
                        'jenisantrian_id',
                        // 'fungsi_antrian_id',
                        'groupcarabayar_id',
                        'kode_antrian',
                        'is_active',
                    ],'required', 'on' => 'cara-bayar'
                ],
                [
                    [
                        'jenisantrian_id',
                        // 'fungsi_antrian_id',
                        // 'groupcarabayar_id',
                        'kode_antrian',
                        'is_active',
                    ],'required', 'on' => 'kasir'
                ],
                [
                    [
                        'jenisantrian_id',
                        'klasifikasipasien_id',
                        // 'fungsi_antrian_id',
                        'groupcarabayar_id',
                        'kode_antrian',
                        'is_active',
                    ],'required', 'on' => 'pendaftaran'
                ],
                [
                    [
                        'jenisantrian_id',
                        'kode_antrian',
                        'instalasi_id',
                        'is_active',
                    ],'required', 'on' => 'penunjang'
                ],
                [
                    [
                        'jenisantrian_id',
                        'instalasi_id',
                        // 'ruangan_id',
                        'fungsi_antrian_id',
                        'kode_antrian',
                        'is_active',
                    ],'required', 'on' => 'farmasi'
                ],
                [
                    [
                        'jenisantrian_id',
                        'instalasi_id',
                        'ruangan_id',
                        'pegawai_id',
                        // 'fungsi_antrian_id',
                        // 'groupcarabayar_id',
                        'kode_antrian',
                        'is_active',
                    ],'required', 'on' => 'poliklinik'
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisantrian_nama' => 'Jenis Antrian',
            'fungsi_antrian_id' => 'Jenis Pengambilan Antrian',
            'groupcarabayar_id' => 'Cara Bayar',
            'klasifikasipasien_id' => 'Klasifikasi Pasien',
            'kode_antrian' => 'Kode Antrian',
            'instalasi_id' => 'Instalasi',
            'ruangan_id' => 'Ruangan',
            'is_active' => 'Status',
            'jenisantrian_id' => 'Jenis Antrian',
            'pegawai_id' => 'Dokter'
        ];
    }
}
