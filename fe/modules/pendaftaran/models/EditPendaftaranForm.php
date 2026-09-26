<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\models;

use Yii;

class EditPendaftaranForm extends \app\components\DocoBaseModel
{

    public $no_rekam_medik;
    public $nama_pasien;
    public $photopasien;
    public $no_mobile_pasien;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $kelaspelayanan_nama;
    public $carabayar_nama;
    public $penjamin_nama;
    public $tgl_admisi;
    public $jeniskasuspenyakit_id;
    public $kelaspelayanan_id;
    public $is_multi_payer;
    public $carabayar_id;
    public $penjamin_id;
    public $nosep;
    public $keterangan;
    public $pegawai_id;
    public $jenis;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $ruangan_id;
    public $kamarruangan_id;
    public $penanggungbiaya_id;
    public $is_pasientitipan;
    public $is_aps;
    public $kamarruangan_nokamar;
    public $group_carabayar;
    public $kamartempattidur_id;
    public $allow_bpjs;
    public $is_bpjs;
    public $jenis_kelamin;
    public $tanggal_lahir;
    public $nomor_urut;
    public $temp_nomor_urut;
    public $limit_tagihan;
    public $styrujukaninstalasi_id;
    public $instalasiasal_id;
    public $dokterpengganti_id;
    public $dokterpengirim_id;
    public $dokterkonsul_id;
    public $hakkelas_id;
    public $kelaspermintaan_id;
    public $prosedurmasuk_id;
    public $diagnosa_awal;
    public $konfig_referral_required;
    public $referal;

    // // case form ausransi
    // public $nokartuasuransi;
    // public $namapemilikasuransi;
    // public $nomorpokokperusahaan;
    // public $kelastanggunganasuransi_id;
    // public $namaperusahaan;
    // public $tgl_konfirmasi;
    // public $status_konfirmasi;

    // public $asuransi = [
    //     'nokartuasuransi',
    //     'namapemilikasuransi',
    //     'nomorpokokperusahaan',
    //     'kelastanggunganasuransi_id',
    //     'namaperusahaan',
    //     'tgl_konfirmasi',
    //     'status_konfirmasi'
    // ];

    protected $xssProtected = [
        'keterangan',
        'nosep',
        'nomor_urut'
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [[
                'keterangan',
                'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
            ], 'required', 'on' => 'edit-bpjs'],
            [[
                'keterangan',
                'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'nosep',
            ], 'required', 'on' => 'edit-bpjs-w-sep'],
            [[
                'keterangan',
                // 'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
            ], 'required', 'on' => 'default'],
            [[
                // 'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
            ], 'required', 'on' => 'st-yusup'],
            [[
                'keterangan',
                // 'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'styrujukaninstalasi_id',
                'ruangan_id',
                'dokterpengirim_id',
            ], 'required', 'on' => 'st-yusup-penunjang'],
            [[
                'keterangan',
                'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'kamarruangan_nokamar',
                'dokterpengirim_id',
            ], 'required', 'on' => 'ranap-sty'],
            [[
                'keterangan',
                'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'styrujukaninstalasi_id',
                'ruangan_id',
            ], 'required', 'on' => 'st-yusup-penunjang'],
            [[
                'keterangan',
                'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'nomor_urut',
            ], 'required', 'on' => 'default-nomor-urut'],
            [[
                'keterangan',
                'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'kamarruangan_nokamar'
            ], 'required', 'on' => 'ranap'],
            [
                [
                    'referal'
                ],
                'required',
                'when' => function ($model) {
                    return $model->konfig_referral_required;
                },
                'whenClient' => "function (attribute, value) {
                    return $('[name=\'konfig_referral_required\']').val() == 1;
                }",
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [[
                'no_rekam_medik',
                'nama_pasien',
                'photopasien',
                'no_mobile_pasien',
                'no_pendaftaran',
                'tgl_pendaftaran',
                'kelaspelayanan_nama',
                'carabayar_nama',
                'penjamin_nama',
                'tgl_admisi',
                'jeniskasuspenyakit_id',
                'kelaspelayanan_id',
                'is_multi_payer',
                'carabayar_id',
                'penjamin_id',
                'nosep',
                'keterangan',
                'pegawai_id',
                'jenis',
                'pendaftaran_id',
                'group_carabayar',
                'pasienadmisi_id',
                'ruangan_id',
                'kamarruangan_id',
                'penanggungbiaya_id',
                'is_pasientitipan',
                'is_aps',
                'kamarruangan_nokamar',
                'kamartempattidur_id',
                'allow_bpjs',
                'is_bpjs',
                'jenis_kelamin',
                'tanggal_lahir',
                'limit_tagihan',
                'nomor_urut',
                'styrujukaninstalasi_id',
                'instalasiasal_id',
                'dokterpengganti_id',
                'dokterpengirim_id',
                'dokterkonsul_id',
                'hakkelas_id',
                'kelaspermintaan_id',
                'prosedurmasuk_id',
                'diagnosa_awal',
                'referal',
                // 'asuransi',
                // 'nokartuasuransi',
                // 'namapemilikasuransi',
                // 'nomorpokokperusahaan',
                // 'kelastanggunganasuransi_id',
                // 'namaperusahaan',
                // 'tgl_konfirmasi',
                // 'status_konfirmasi',
            ],'safe'],
            [[
                'pendaftaran_id',
                'carabayar_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'ruangan_id',
                'pasienadmisi_id',
                'kamarruangan_id',
            ], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_rekam_medik' => 'No Rekam Medik',
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit',
            'pegawai_id' => 'Dokter DPJP',
            'kelaspelayanan_id' => 'Kelas Pelayanan',
            'carabayar_id' => 'Cara Bayar',
            'penjamin_id' => 'Penjamin',
            'keterangan' => 'Keterangan Pendaftaran',
            'nosep' => 'No. SEP',
            'penanggungbiaya_id' => 'Penanggung Biaya',
            'nomor_urut' => 'Nomor Urut',
            'limit_tagihan' => 'Limit Tagihan',
            'styrujukaninstalasi_id' => 'Rujukan Dari',
            'instalasiasal_id' => 'Penunjang Medis',
            'ruangan_id' => 'Ruangan',
            'dokterpengganti_id' => 'Dokter Pengganti',
            'dokterpengirim_id' => 'Dokter Pengirim',
            'dokterkonsul_id' => 'Dokter Konsul',
            'hakkelas_id' => 'Hak Kelas',
            'kelaspermintaan_id' => 'Kelas Permintaan',
            'prosedurmasuk_id' => 'Prosedur Masuk',
            'diagnosa_awal' => 'Diagnosa Awal',
            'referal_luar' => 'Referral Luar',
            'referal_pegawai_nama' => 'Referral Pegawai',
            'referal' => 'Referral',
            'jeniskasuspenyakit_nama' => 'Jenis Kasus Penyakit',
            'nama_pegawai' => 'Dokter DPJP',
            'carabayar_nama' => 'Cara Bayar',
            'penjamin_nama' => 'Penjamin',
            'admisi_carabayar_nama' => 'Cara Bayar Rawat Inap',
            'admisi_nama_pegawai' => 'Dokter DPJP Rawat Inap',
            'admisi_penjamin_nama' => 'Penjamin Rawat Inap',
            'admisi_kelaspelayanan_nama' => 'Kelas Pelayanan Rawat Inap',
            'admisi_kamarruangan_nokamar' => 'Kamar Ruangan Rawat Inap',
            'admisi_no_tempattidur' => 'Tempat Tidur Rawat Inap',
            'admisi_ruangan_nama' => 'Ruangan Rawat Inap',
            // 'namapemilikasuransi' => 'Nama Pemilik Asuransi',
            // 'nomorpokokperusahaan' => 'Nomor Pokok Perusahaan',
            // 'kelastanggungan_id' => 'Kelas Tanggungan',
            // 'namaperusahaan' => 'Nama Perusahaan',
            // 'tgl_konfirmasi' => 'Tanggal Konfirmasi',
            // 'status_konfirmasi' => 'Status Konfirmasi',
            // 'kelastanggunganasuransi_id' => 'Kelas Tanggungan Asuransi',
            // 'nokartuasuransi' => 'No Kartu Asuransi'
        ];
    }

}
