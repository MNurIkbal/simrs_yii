<?php

namespace app\modules\pendaftaran\models;

use Yii;

class BpjsNewForm extends \yii\base\Model
{
    public $jenis_rujukan; // 1: rujukan, 2: rujukan manual / IGD

    public $tanggal_sep;
    public $jenis_pelayanan; // 1: Rawat Inap, 2. Rawat Jalan --------case rujukan manual
    public $jenis_kartu; // 1: bpjs, 2: NIK --------------------------case rujukan manual
    public $no_kartu; // ----------------------------------------------case rujukan manual

    public $poli_tujuan;
    public $poli_eksekutif;

    public $asal_rujukan; // 1: Faskes Tingkat 1, 2: Faskes Tingkat 2 (RS)
    public $ppk_rujukan;
    public $no_rujukan;
    public $no_rujukan_f;
    public $tanggal_rujukan;
    public $no_rekam_medik;
    public $cob;
    public $kelas_rawat;
    public $diagnosa_awal;
    public $no_telp;
    public $catatan_sep;
    public $katarak;
    public $kasus_kecelakaan;
    public $tanggal_kejadian;
    public $kode_provinsi;
    public $kode_kabupaten;
    public $kode_kecamatan;
    public $keterangan;

    public $status_suplesi;
    public $no_sep_suplesi;

    public $no_surat_kontrol;
    public $kode_dpjp;
    public $user;
    public $pendaftaran_id;
    public $no_asuransi;
    public $noKartu;
    public $pasienadmisi_id;
    public $nama_pasien;
    public $nosep;
    public $jenis_peserta;
    public $penjamin;
    public $asuransi;
    public $hak_kelas;
    public $jenis_kelamin;
    public $tanggal_lahir;
    public $noMr;
    public $ruangan_id;
    public $kode_dpjp_melayani;
    public $nama_dpjp_melayani;
    public $kode_ppk_perujuk;
    public $nama_ppk_perujuk;
    public $tujuanKunj;
    public $flagProcedure;
    public $kdPenunjang;
    public $assesmentPel;
    public $is_tujuan_kunj;
    public $kode_dpjp_spri;
    public $nama_dpjp_spri;
    public $info_response;
    public $no_lp;
    public $is_naikkelas_ranap;
    public $naik_kelas_rawat_inap;
    public $pembiayaan;
    public $nama_penganggung_jawab;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'asal_rujukan',
                    'jenis_pelayanan',
                    // 'no_rujukan',
                    'no_kartu',
                    'tanggal_sep',
                    'tanggal_rujukan',
                    'diagnosa_awal',
                    'no_telp',
                    'kasus_kecelakaan',
                    // 'ppk_rujukan',
                    // 'jenis_peserta'
                ],
                'required'
            ],
            [
                ['no_rujukan', 'ppk_rujukan'],
                'safe',
                'on' => ['igd','igdskdp','igdskdpsuplesi','igdskdpsuplesi','igdskdpkll','igdkll']
            ],
            [
                [/* 'no_rujukan', */ 'ppk_rujukan'],
                'required',
                'on' => ['rajal','rajalskdp', 'rajalsepbackdate', 'rajalskdpsuplesi','rajalskdpsuplesi','rajalskdpkll','rajalkll']
            ],
            [
                ['no_rujukan', 'ppk_rujukan'],
                'required',
                'on' => ['ranap','ranapskdp','ranapskdpsuplesi','ranapskdpsuplesi','ranapskdpkll','ranapkll']
            ],
            [
                ['poli_tujuan'], 'required', 'on' => 'rajal'
            ],
            [
                [
                    'poli_eksekutif', 'cob', 'kelas_rawat',
                    'catatan_sep', 'katarak', 'tanggal_kejadian', 'keterangan',
                    'no_surat_kontrol', 'kode_dpjp', 'status_suplesi', 'no_sep_suplesi',
                    'kode_provinsi', 'kode_kabupaten', 'kode_kecamatan', 'user', 'no_rujukan_f',
                    'no_rujukan', 'no_telp', 'pendaftaran_id', 'pasienadmisi_id','poli_tujuan',
                    'jenis_kartu', 'jenis_rujukan','no_kartu','no_rekam_medik','jenis_pelayanan', 'ppk_rujukan', 'kode_dpjp_melayani', 'nama_dpjp_melayani', 'nama_ppk_perujuk', 'kode_ppk_perujuk',
                    'tujuanKunj', 'flagProcedure', 'kdPenunjang', 'assesmentPel', 'is_tujuan_kunj', 'kode_dpjp_spri', 'nama_dpjp_spri', 'info_response', 'jenis_peserta', 'no_lp', 'is_naikkelas_ranap'
                ],
                'safe'
            ],
            [
                ['kode_dpjp', 'kode_dpjp_melayani'],
                'required',
                'on'=>['skdp','igdskdp','rajalskdp']
            ],
            [
                ['kode_dpjp', 'ppk_rujukan', 'no_surat_kontrol', 'no_rujukan'],
                'required',
                'on'=>['skdpranap']
            ],
            // [
            //     ['kode_dpjp_melayani'],
            //     'required',
            //     'on'=>['default']
            // ],
            [
                ['kode_dpjp', 'no_sep_suplesi'],
                'required',
                'on'=>['skdpsuplesi','igdskdpsuplesi','rajalskdpsuplesi','ranapskdpsuplesi']
            ],
            [
                ['no_sep_suplesi'],
                'required',
                'on'=>['suplesi','igdsuplesi','rajalsuplesi','ranapsuplesi']
            ],
            [
                ['kode_dpjp', 'kode_provinsi', 'kode_kabupaten', 'kode_kecamatan'],
                'required',
                'on'=>['skdpkll','igdskdpkll','rajalskdpkll','ranapskdpkll']
            ],
            [
                ['kode_provinsi', 'kode_kabupaten', 'kode_kecamatan'],
                'required',
                'on'=>['kll','igdkll','rajalskdp', 'rajalsepbackdate', 'ranapskdp'],
                // 'on'=>['kll','igdkll','rajalskdp','ranapskdp']
                'on'=>['kll']
            ],
            [
                ['kode_provinsi', 'kode_kabupaten', 'kode_kecamatan'],
                'default',
                'value'=>''
            ],
            [
                ['jenis_peserta'],
                'required',
                'on'=>['ranapskdp', 'rajalskdp', 'igdjenispeserta']
            ],
            [
                ['kode_dpjp_melayani'],
                'required',
                'on'=>['skdpbedapoli']
            ],
            [
                ['kode_dpjp_melayani', 'jenis_peserta',],
                'required',
                'on'=>['skdpbedapolisty']
            ],
            [
                ['kode_dpjp', 'kode_dpjp_melayani', 'jenis_peserta',],
                'required',
                'on'=>['skdpsty']
            ],
            [
                ['kode_dpjp', 'no_sep_suplesi', 'jenis_peserta',],
                'required',
                'on'=>['skdpsuplesisty']
            ],
            [
                ['kode_dpjp', 'kode_provinsi', 'kode_kabupaten', 'kode_kecamatan', 'jenis_peserta',],
                'required',
                'on'=>['skdpkllsty']
            ],
            [
                [
                    'asal_rujukan',
                    'jenis_pelayanan',
                    'no_kartu',
                    'tanggal_sep',
                    'tanggal_rujukan',
                    'diagnosa_awal',
                    'no_telp',
                    'kasus_kecelakaan',
                    'jenis_peserta',
                ], 'required',
                'on'=>['defaultsty']
            ],
            // [
            //     [
            //         'no_telp',
            //     ],
            //     'string', 'min' => 8, 'max' => 20
            // ],
            [
                [
                    'no_lp',
                ],
                'string', 'min' => 0, 'max' => 100
            ],
            [
                [
                    'naik_kelas_rawat_inap',
                    'pembiayaan',
                    'nama_penganggung_jawab'
                ],
                'required',
                'when' => function($model) {
                      return isset($model->is_naikkelas_ranap) && $model->is_naikkelas_ranap == true;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_rujukan_f' => Yii::t('fe','No Rujukan'),
            'jenis_rujukan' => Yii::t('fe','Jenis Pencarian'),
            'tanggal_sep' => Yii::t('fe','Tanggal SEP'),
            'asal_rujukan' => Yii::t('fe','Asal Rujukan'),
            'jenis_pelayanan' => Yii::t('fe','Pelayanan'),
            'ppk_rujukan' => Yii::t('fe','PPK Asal Peserta'),
            'jenis_kartu' => Yii::t('fe','Jenis Kartu'),
            'no_rujukan' => Yii::t('fe','No Rujukan'),
            'no_kartu' => Yii::t('fe','No Kartu'),

            'poli_tujuan'=> Yii::t('fe','Spesialis / SubSpesialis'),
            'poli_eksekutif'=> Yii::t('fe','Eksekutif'),
            'cob'=> Yii::t('fe','COB'),
            'diagnosa_awal'=> Yii::t('fe','Diagnosa'),

            'no_surat_kontrol'=> Yii::t('fe','No.Surat Kontrol/SKDP'),
            'kode_dpjp'=> Yii::t('fe','DPJP Pemberi Surat SKDP/SPRI'),
            'kode_dpjp_melayani'=> Yii::t('fe','DPJP Melayani'),
            'nama_dpjp_melayani'=> Yii::t('fe','Nama DPJP Melayani'),
            'nosep' => Yii::t('fe','No. SEP'),
            'kasus_kecelakaan' => Yii::t('fe','Status Kecelakaan'),
            'nama_ppk_perujuk' => Yii::t('fe','Nama PPK Asal Peserta'),
            'kode_ppk_perujuk' => Yii::t('fe','Kode PPK Asal Peserta'),
            'kode_dpjp_spri'=> Yii::t('fe','DPJP Pemberi Spri'),
            'nama_dpjp_spri'=> Yii::t('fe','Nama DPJP Pemberi Spri'),
            'jenis_peserta'=> Yii::t('fe','Jenis Peserta'),
            'no_lp'=> Yii::t('fe','No. LP'),
            'is_naikkelas_ranap'=> Yii::t('fe','Naik Kelas Rawat Inap'),
            'naik_kelas_rawat_inap'=> Yii::t('fe','Kelas Rawat Inap'),
            'pembiayaan'=> Yii::t('fe','Pembiayaan'),
            'nama_penganggung_jawab'=> Yii::t('fe','Nama Penanggung Jawab'),
        ];
    }
}
