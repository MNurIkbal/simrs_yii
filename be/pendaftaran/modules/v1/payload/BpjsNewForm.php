<?php

namespace app\modules\v1\payload;

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
    public $no_lp;

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
    public $kode_dpjp_melayani;
    public $jenis_peserta;
    public $nama_dpjp_melayani;
    public $kode_ppk_perujuk;
    public $nama_ppk_perujuk;
    public $klsRawatNaik;
    public $pembiayaan;
    public $penanggungJawab;
    public $tujuanKunj;
    public $flagProcedure;
    public $kdPenunjang;
    public $assesmentPel;
    public $kode_dpjp_spri;
    public $nama_dpjp_spri;
    public $info_response;

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
                    'no_kartu',
                    'tanggal_sep',
                    'tanggal_rujukan',
                    'diagnosa_awal',
                    'no_telp',
                    'kasus_kecelakaan',
                ],
                    'required', 'on' => 'default'
            ],
            [
                ['no_kartu'], 'required', 'on' => 'unauth'
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
                    'jenis_kartu', 'jenis_rujukan','no_kartu','no_rekam_medik','ppk_rujukan', 'kode_dpjp_melayani', 'jenis_peserta', 'nama_dpjp_melayani', 'nama_ppk_perujuk', 'kode_ppk_perujuk', 'nama_peserta', 'nomorpokokperusahaan',
                    'namaperusahaan', 'skip_bpjs', 'klsRawatNaik', 'pembiayaan', 'penanggungJawab', 'tujuanKunj', 'flagProcedure', 'kdPenunjang', 'assesmentPel','kode_dpjp_spri', 'nama_dpjp_spri', 'info_response', 'no_lp'
                ],
                'safe'
            ],
            [
                ['no_surat_kontrol', 'kode_dpjp', 'kode_dpjp_melayani'],
                'required',
                'on'=>'skdp'
            ],
            [
                ['no_surat_kontrol', 'kode_dpjp', 'no_sep_suplesi', 'kode_dpjp_melayani'],
                'required',
                'on'=>'skdpsuplesi'
            ],
            [
                ['no_sep_suplesi'],
                'required',
                'on'=>'suplesi'
            ],
            [
                ['no_surat_kontrol', 'kode_dpjp', 'kode_provinsi', 'kode_kabupaten', 'kode_kecamatan', 'kode_dpjp_melayani'],
                'required',
                'on'=>'skdpkll'
            ],
            [
                ['kode_provinsi', 'kode_kabupaten', 'kode_kecamatan'],
                'required',
                'on'=>'kll'
            ],
            [
                ['kode_provinsi', 'kode_kabupaten', 'kode_kecamatan', 'klsRawatNaik', 'pembiayaan', 'penanggungJawab', 'flagProcedure', 'kdPenunjang', 'kode_dpjp_melayani'],
                'default',
                'value'=>''
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_rujukan_f' => 'No Rujukan',
            'jenis_rujukan' => 'Jenis Pencarian',
            'tanggal_sep' => 'Tanggal SEP',
            'asal_rujukan' => 'Asal Rujukan',
            'jenis_pelayanan' => 'Pelayanan',
            'ppk_rujukan' => 'PPK Asal Peserta',
            'jenis_kartu' => 'Jenis Kartu',
            'no_rujukan' => 'No Rujukan',
            'no_kartu' => 'No Kartu',

            'poli_tujuan'=> 'Spesialis / SubSpesialis',
            'poli_eksekutif'=> 'Eksekutif',
            'cob'=> 'COB',
            'diagnosa_awal'=> 'Diagnosa',

            'no_surat_kontrol'=> 'No.Surat Kontrol/SKDP',
            'kode_dpjp'=> 'DPJP Pemberi Surat SKDP/SPRI',
            'kode_dpjp_melayani'=> 'DPJP Melayani',
            'nama_dpjp_melayani'=> 'Nama DPJP Melayani',
            'nama_ppk_perujuk'=> 'Nama PPK Asal Peserta',
            'kode_ppk_perujuk'=> 'Kode PPK Asal Peserta',
            'kode_dpjp_spri'=> 'DPJP Pemberi Spri',
            'nama_dpjp_spri'=> 'Nama DPJP Pemberi Spri',
        ];
    }
}
