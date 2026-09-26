<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-11 15:00:41
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-01-21 15:21:50
 */

namespace app\modules\pendaftaran\models;

use Yii;

class KunjunganForm extends \yii\base\Model
{
    public $tgl_pendaftaran;
    public $ruangan_id;
    public $jeniskasuspenyakit_id;
    public $kelaspelayanan_id;
    public $dokter_id;
    public $pegawai_id; // as dokter_id in pendaftaran penunjang
    public $carabayar_id;
    public $penjamin_id;
    public $keadaan_masuk;
    public $transportasi;
    public $keterangan;
    public $rujukan_id;
    public $instalasi_id;
    public $asalrujukan_id;
    public $asuransipasien_id;
    public $bpjs_id;
    public $catatanpenting_pasien;
    public $namadepan;
    public $nama_pasien;
    public $propinsi_id;
    public $kabupaten_id;
    public $kecamatan_id;
    public $kelurahan_id;
    public $rt;
    public $rw;
    public $kode_pos;
    public $alamat_pasien;
    public $no_telepon_pasien;
    public $pekerjaan_id;
    public $pt;
    public $group_carabayar;
    public $list_penunjang;
    public $referal;
    public $konfig_referral_required;
    public $jadwaldokter_id;

    public $is_pj;

    // penanggungjawab
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
    public $pendaftaranol_id;
    public $tindakan_karcis;
    public $list_paket;
    public $list_pasien_mcu;
    public $is_skd;
    public $nomor_urut;
    public $limit_tagihan;
    public $no_exportexcel;
    public $dokterpengirim_id;
    public $styrujukaninstalasi_id;
    public $waktu_kunjungan;
    public $dokter_pengganti_id;
    public $status_kunjungan;
    public $list_fisio;
    public $diagnosa;

	public function rules()
    {
         return [
            [
                [
                    'tgl_pendaftaran', 'ruangan_id', 'jeniskasuspenyakit_id',
                    'kelaspelayanan_id', 'carabayar_id','penjamin_id','asalrujukan_id'
                ],
                'required', 'on' => 'with_mandatory_pjawab',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran', 'ruangan_id', 'jeniskasuspenyakit_id',
                    'kelaspelayanan_id', 'carabayar_id','penjamin_id','asalrujukan_id', 'pegawai_id',
                ],
                'required', 'on' => 'kunjungan_penunjang',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran', 'ruangan_id', 'dokterpengirim_id', 'styrujukaninstalasi_id', 'instalasi_id'
                ],
                'required', 'on' => 'kunjungan_penunjang_styp',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran', 'ruangan_id'
                ],
                'required', 'on' => 'kunjungan_rj_styp',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran', 'kelaspelayanan_id'
                ],
                'required', 'on' => 'kunjungan_ri_styp',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran',
                    'ruangan_id',
                    'jeniskasuspenyakit_id',
                    'kelaspelayanan_id',
                    'pegawai_id'
                ],
                'required', 'on' => 'form_kunjugan',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran', 'ruangan_id', 'jeniskasuspenyakit_id',
                    'kelaspelayanan_id', 'carabayar_id','penjamin_id','asalrujukan_id', 'nomor_urut'
                ],
                'required', 'on' => 'with_mandatory_pjawab_nourut',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'ruangan_id',
                    'jeniskasuspenyakit_id',
                    'kelaspelayanan_id'
                ],
                'required', 'on' => 'form_kunjugan_ranap',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'tgl_pendaftaran', 'ruangan_id', 'jeniskasuspenyakit_id',
                    'kelaspelayanan_id', 'carabayar_id','penjamin_id','asalrujukan_id',
                    'pj_pengantar', 'pj_nama', 'pj_jk','instalasi_id',
                ],
                'required', 'on' => 'with_mandatory_all',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'ruangan_id'
                ],
                'required', 'on' => 'form_kunjungan_reservasi_mcu',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
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
            [
                [
                    'keadaan_masuk', 'transportasi','keterangan','instalasi_id', 'dokterpengirim_id', 'styrujukaninstalasi_id',
                    'namadepan', 'nama_pasien', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'rt', 'rw', 'kode_pos', 'alamat_pasien', 'no_telepon_pasien', 'pekerjaan_id', 'pt',
                    'asuransipasien_id', 'bpjs_id','rujukan_id', 'catatanpenting_pasien', 'no_exportexcel',
                    'pj_jenis_identitas', 'pj_no_identitas', 'pj_hubungan', 'pj_tempat_lahir',
                    'pj_tanggal_lahir', 'pj_umur', 'pj_alamat', 'pj_no_telepon', 'dokter_id', 'pegawai_id', 'group_carabayar',
                    'pendaftaranol_id', 'is_pj','tindakan_karcis', 'list_penunjang', 'list_paket', 'list_pasien_mcu', 'is_skd', 'nomor_urut', 'limit_tagihan', 'waktu_kunjungan', 'dokter_pengganti_id', 'status_kunjungan', 'diagnosa',
                    'referal', 'jadwaldokter_id', 'konfig_referral_required'
                ],
                'safe'
            ],
        ];
    }

    public function attributeLabels()
    {
        switch( $this->getScenario() )
        {
            case 'kunjungan_penunjang_styp':
                return [
                    'tgl_pendaftaran' => \Yii::t('fe', 'Tanggal pendaftaran'),
                    'ruangan_id' => \Yii::t('fe', 'Ruangan'),
                    'jeniskasuspenyakit_id' => \Yii::t('fe', 'Jenis kasus penyakit'),
                    'kelaspelayanan_id' => \Yii::t('fe', 'Kelas pelayanan'),
                    'dokter_id' => \Yii::t('fe', 'Dokter'),
                    'pegawai_id' => \Yii::t('fe', 'Dokter'),
                    'carabayar_id' => \Yii::t('fe', 'Cara bayar'),
                    'penjamin_id' => \Yii::t('fe', 'Penjamin'),
                    'keadaan_masuk'=> \Yii::t('fe', 'Keadaan masuk'),
                    'transportasi'=> \Yii::t('fe', 'Transportasi'),
                    'rujukan_id'=> \Yii::t('fe', 'Rujukan'),
                    'instalasi_id'=> \Yii::t('fe', 'Penunjang Medis'),
                    'keterangan'=> \Yii::t('fe', 'Keterangan pendaftaran'),
                    'asalrujukan_id'=> \Yii::t('fe', 'Asal rujukan'),
                    'asuransipasien_id'=> \Yii::t('fe', 'Asuransi pasien'),
                    'catatanpenting_pasien'=> \Yii::t('fe', 'Catatan Penting Pasien'),
                    'namadepan'=> \Yii::t('fe', 'Nama Depan'),
                    'nama_pasien'=> \Yii::t('fe', 'Nama Pasien'),
                    'propinsi_id'=> \Yii::t('fe', 'Propinsi'),
                    'kabupaten_id'=> \Yii::t('fe', 'Kabupaten'),
                    'kecamatan_id'=> \Yii::t('fe', 'Kecamatan'),
                    'kelurahan_id'=> \Yii::t('fe', 'Kelurahan'),
                    'rt'=> \Yii::t('fe', 'RT'),
                    'rw'=> \Yii::t('fe', 'RW'),
                    'kode_pos'=> \Yii::t('fe', 'Kode Pos'),
                    'alamat_pasien'=> \Yii::t('fe', 'Alamat Pasien'),
                    'no_telepon_pasien'=> \Yii::t('fe', 'No Telepon Pasien'),
                    'pekerjaan_id'=> \Yii::t('fe', 'pekerjaan_id'),
                    'pt'=> \Yii::t('fe', 'PT'),
                    'dokterpengirim_id'=> \Yii::t('fe', 'Dokter Pengirim'),
                    'styrujukaninstalasi_id'=> \Yii::t('fe', 'Rujukan Dari'),

                    'pj_pengantar' => \Yii::t('fe', 'Pengantar'),
                    'pj_nama' => \Yii::t('fe', 'Nama'),
                    'pj_jk' => \Yii::t('fe', 'Jenis kelamin'),
                    'pj_jenis_identitas' => \Yii::t('fe', 'Jenis identitas'),
                    'pj_no_identitas' => \Yii::t('fe', 'No identitas'),
                    'pj_hubungan' => \Yii::t('fe', 'Hubungan'),
                    'pj_tempat_lahir' => \Yii::t('fe', 'Tempat lahir'),
                    'pj_tanggal_lahir'=> \Yii::t('fe', 'Tanggal lahir'),
                    'pj_umur'=> \Yii::t('fe', 'Umur'),
                    'pj_alamat'=> \Yii::t('fe', 'Alamat'),
                    'pj_no_telepon'=> \Yii::t('fe', 'No telepon'),
                    'is_pj'=> \Yii::t('fe', 'Penangung Jawab'),
                    'is_skd'=> \Yii::t('fe', 'Surat Keterangan Dokter'),
                    'nomor_urut'=> \Yii::t('fe', 'Nomor Urut'),
                    'limit_tagihan'=> \Yii::t('fe', 'Limit Tagihan'),
                    'dokter_pengganti_id'=> \Yii::t('fe', 'Dokter Pengganti'),
                    'status_kunjungan'=> \Yii::t('fe', 'Kunjungan'),
                    'diagnosa'=> \Yii::t('fe', 'Jenis Pemeriksaan'),
                    'referal' => \Yii::t('fe', 'Referral'),
                ];
                break;
            case 'kunjungan_rj_styp':
                return [
                    'tgl_pendaftaran' => \Yii::t('fe', 'Tanggal pendaftaran'),
                    'ruangan_id' => \Yii::t('fe', 'Klinik'),
                    'jeniskasuspenyakit_id' => \Yii::t('fe', 'Jenis kasus penyakit'),
                    'kelaspelayanan_id' => \Yii::t('fe', 'Kelas pelayanan'),
                    'dokter_id' => \Yii::t('fe', 'Dokter'),
                    'pegawai_id' => \Yii::t('fe', 'Dokter'),
                    'carabayar_id' => \Yii::t('fe', 'Cara bayar'),
                    'penjamin_id' => \Yii::t('fe', 'Penjamin'),
                    'keadaan_masuk'=> \Yii::t('fe', 'Keadaan masuk'),
                    'transportasi'=> \Yii::t('fe', 'Transportasi'),
                    'rujukan_id'=> \Yii::t('fe', 'Rujukan'),
                    'instalasi_id'=> \Yii::t('fe', 'Penunjang Medis'),
                    'keterangan'=> \Yii::t('fe', 'Keterangan pendaftaran'),
                    'asalrujukan_id'=> \Yii::t('fe', 'Asal rujukan'),
                    'asuransipasien_id'=> \Yii::t('fe', 'Asuransi pasien'),
                    'catatanpenting_pasien'=> \Yii::t('fe', 'Catatan Penting Pasien'),
                    'namadepan'=> \Yii::t('fe', 'Nama Depan'),
                    'nama_pasien'=> \Yii::t('fe', 'Nama Pasien'),
                    'propinsi_id'=> \Yii::t('fe', 'Propinsi'),
                    'kabupaten_id'=> \Yii::t('fe', 'Kabupaten'),
                    'kecamatan_id'=> \Yii::t('fe', 'Kecamatan'),
                    'kelurahan_id'=> \Yii::t('fe', 'Kelurahan'),
                    'rt'=> \Yii::t('fe', 'RT'),
                    'rw'=> \Yii::t('fe', 'RW'),
                    'kode_pos'=> \Yii::t('fe', 'Kode Pos'),
                    'alamat_pasien'=> \Yii::t('fe', 'Alamat Pasien'),
                    'no_telepon_pasien'=> \Yii::t('fe', 'No Telepon Pasien'),
                    'pekerjaan_id'=> \Yii::t('fe', 'pekerjaan_id'),
                    'pt'=> \Yii::t('fe', 'PT'),
                    'dokterpengirim_id'=> \Yii::t('fe', 'Dokter Pengirim'),
                    'styrujukaninstalasi_id'=> \Yii::t('fe', 'Rujukan Dari'),

                    'pj_pengantar' => \Yii::t('fe', 'Pengantar'),
                    'pj_nama' => \Yii::t('fe', 'Nama'),
                    'pj_jk' => \Yii::t('fe', 'Jenis kelamin'),
                    'pj_jenis_identitas' => \Yii::t('fe', 'Jenis identitas'),
                    'pj_no_identitas' => \Yii::t('fe', 'No identitas'),
                    'pj_hubungan' => \Yii::t('fe', 'Hubungan'),
                    'pj_tempat_lahir' => \Yii::t('fe', 'Tempat lahir'),
                    'pj_tanggal_lahir'=> \Yii::t('fe', 'Tanggal lahir'),
                    'pj_umur'=> \Yii::t('fe', 'Umur'),
                    'pj_alamat'=> \Yii::t('fe', 'Alamat'),
                    'pj_no_telepon'=> \Yii::t('fe', 'No telepon'),
                    'is_pj'=> \Yii::t('fe', 'Penangung Jawab'),
                    'is_skd'=> \Yii::t('fe', 'Surat Keterangan Dokter'),
                    'nomor_urut'=> \Yii::t('fe', 'Nomor Urut'),
                    'limit_tagihan'=> \Yii::t('fe', 'Limit Tagihan'),
                    'dokter_pengganti_id'=> \Yii::t('fe', 'Dokter Pengganti'),
                    'status_kunjungan'=> \Yii::t('fe', 'Kunjungan'),
                    'diagnosa'=> \Yii::t('fe', 'Jenis Pemeriksaan'),
                    'referal' => \Yii::t('fe', 'Referral'),
                ];
                break;
            case 'kunjungan_ri_styp':
                return [
                    'tgl_pendaftaran' => \Yii::t('fe', 'Tanggal pendaftaran'),
                    'ruangan_id' => \Yii::t('fe', 'Klinik'),
                    'jeniskasuspenyakit_id' => \Yii::t('fe', 'Jenis kasus penyakit'),
                    'kelaspelayanan_id' => \Yii::t('fe', 'Kelas Perawatan'),
                    'dokter_id' => \Yii::t('fe', 'Dokter'),
                    'pegawai_id' => \Yii::t('fe', 'Dokter'),
                    'carabayar_id' => \Yii::t('fe', 'Cara bayar'),
                    'penjamin_id' => \Yii::t('fe', 'Penjamin'),
                    'keadaan_masuk'=> \Yii::t('fe', 'Keadaan masuk'),
                    'transportasi'=> \Yii::t('fe', 'Transportasi'),
                    'rujukan_id'=> \Yii::t('fe', 'Rujukan'),
                    'instalasi_id'=> \Yii::t('fe', 'Penunjang Medis'),
                    'keterangan'=> \Yii::t('fe', 'Keterangan pendaftaran'),
                    'asalrujukan_id'=> \Yii::t('fe', 'Asal rujukan'),
                    'asuransipasien_id'=> \Yii::t('fe', 'Asuransi pasien'),
                    'catatanpenting_pasien'=> \Yii::t('fe', 'Catatan Penting Pasien'),
                    'namadepan'=> \Yii::t('fe', 'Nama Depan'),
                    'nama_pasien'=> \Yii::t('fe', 'Nama Pasien'),
                    'propinsi_id'=> \Yii::t('fe', 'Propinsi'),
                    'kabupaten_id'=> \Yii::t('fe', 'Kabupaten'),
                    'kecamatan_id'=> \Yii::t('fe', 'Kecamatan'),
                    'kelurahan_id'=> \Yii::t('fe', 'Kelurahan'),
                    'rt'=> \Yii::t('fe', 'RT'),
                    'rw'=> \Yii::t('fe', 'RW'),
                    'kode_pos'=> \Yii::t('fe', 'Kode Pos'),
                    'alamat_pasien'=> \Yii::t('fe', 'Alamat Pasien'),
                    'no_telepon_pasien'=> \Yii::t('fe', 'No Telepon Pasien'),
                    'pekerjaan_id'=> \Yii::t('fe', 'pekerjaan_id'),
                    'pt'=> \Yii::t('fe', 'PT'),
                    'dokterpengirim_id'=> \Yii::t('fe', 'Dokter Pengirim'),
                    'styrujukaninstalasi_id'=> \Yii::t('fe', 'Rujukan Dari'),

                    'pj_pengantar' => \Yii::t('fe', 'Pengantar'),
                    'pj_nama' => \Yii::t('fe', 'Nama'),
                    'pj_jk' => \Yii::t('fe', 'Jenis kelamin'),
                    'pj_jenis_identitas' => \Yii::t('fe', 'Jenis identitas'),
                    'pj_no_identitas' => \Yii::t('fe', 'No identitas'),
                    'pj_hubungan' => \Yii::t('fe', 'Hubungan'),
                    'pj_tempat_lahir' => \Yii::t('fe', 'Tempat lahir'),
                    'pj_tanggal_lahir'=> \Yii::t('fe', 'Tanggal lahir'),
                    'pj_umur'=> \Yii::t('fe', 'Umur'),
                    'pj_alamat'=> \Yii::t('fe', 'Alamat'),
                    'pj_no_telepon'=> \Yii::t('fe', 'No telepon'),
                    'is_pj'=> \Yii::t('fe', 'Penangung Jawab'),
                    'is_skd'=> \Yii::t('fe', 'Surat Keterangan Dokter'),
                    'nomor_urut'=> \Yii::t('fe', 'Nomor Urut'),
                    'limit_tagihan'=> \Yii::t('fe', 'Limit Tagihan'),
                    'dokter_pengganti_id'=> \Yii::t('fe', 'Dokter Pengganti'),
                    'status_kunjungan'=> \Yii::t('fe', 'Kunjungan'),
                    'diagnosa'=> \Yii::t('fe', 'Jenis Pemeriksaan'),
                    'referal' => \Yii::t('fe', 'Referral'),
                ];
                break;
            default:
            return [
                'tgl_pendaftaran' => \Yii::t('fe', 'Tanggal pendaftaran'),
                'ruangan_id' => \Yii::t('fe', 'Ruangan'),
                'jeniskasuspenyakit_id' => \Yii::t('fe', 'Jenis kasus penyakit'),
                'kelaspelayanan_id' => \Yii::t('fe', 'Kelas pelayanan'),
                'dokter_id' => \Yii::t('fe', 'Dokter'),
                'pegawai_id' => \Yii::t('fe', 'Dokter'),
                'carabayar_id' => \Yii::t('fe', 'Cara bayar'),
                'penjamin_id' => \Yii::t('fe', 'Penjamin'),
                'keadaan_masuk'=> \Yii::t('fe', 'Keadaan masuk'),
                'transportasi'=> \Yii::t('fe', 'Transportasi'),
                'rujukan_id'=> \Yii::t('fe', 'Rujukan'),
                'instalasi_id'=> \Yii::t('fe', 'Instalasi'),
                'keterangan'=> \Yii::t('fe', 'Keterangan pendaftaran'),
                'asalrujukan_id'=> \Yii::t('fe', 'Asal rujukan'),
                'asuransipasien_id'=> \Yii::t('fe', 'Asuransi pasien'),
                'catatanpenting_pasien'=> \Yii::t('fe', 'Catatan Penting Pasien'),
                'namadepan'=> \Yii::t('fe', 'Nama Depan'),
                'nama_pasien'=> \Yii::t('fe', 'Nama Pasien'),
                'propinsi_id'=> \Yii::t('fe', 'Propinsi'),
                'kabupaten_id'=> \Yii::t('fe', 'Kabupaten'),
                'kecamatan_id'=> \Yii::t('fe', 'Kecamatan'),
                'kelurahan_id'=> \Yii::t('fe', 'Kelurahan'),
                'rt'=> \Yii::t('fe', 'RT'),
                'rw'=> \Yii::t('fe', 'RW'),
                'kode_pos'=> \Yii::t('fe', 'Kode Pos'),
                'alamat_pasien'=> \Yii::t('fe', 'Alamat Pasien'),
                'no_telepon_pasien'=> \Yii::t('fe', 'No Telepon Pasien'),
                'pekerjaan_id'=> \Yii::t('fe', 'pekerjaan_id'),
                'pt'=> \Yii::t('fe', 'PT'),
                'dokterpengirim_id'=> \Yii::t('fe', 'Dokter Pengirim'),
                'styrujukaninstalasi_id'=> \Yii::t('fe', 'Rujukan Dari'),

                'pj_pengantar' => \Yii::t('fe', 'Pengantar'),
                'pj_nama' => \Yii::t('fe', 'Nama'),
                'pj_jk' => \Yii::t('fe', 'Jenis kelamin'),
                'pj_jenis_identitas' => \Yii::t('fe', 'Jenis identitas'),
                'pj_no_identitas' => \Yii::t('fe', 'No identitas'),
                'pj_hubungan' => \Yii::t('fe', 'Hubungan'),
                'pj_tempat_lahir' => \Yii::t('fe', 'Tempat lahir'),
                'pj_tanggal_lahir'=> \Yii::t('fe', 'Tanggal lahir'),
                'pj_umur'=> \Yii::t('fe', 'Umur'),
                'pj_alamat'=> \Yii::t('fe', 'Alamat'),
                'pj_no_telepon'=> \Yii::t('fe', 'No telepon'),
                'is_pj'=> \Yii::t('fe', 'Penangung Jawab'),
                'is_skd'=> \Yii::t('fe', 'Surat Keterangan Dokter'),
                'nomor_urut'=> \Yii::t('fe', 'Nomor Urut'),
                'limit_tagihan'=> \Yii::t('fe', 'Limit Tagihan'),
                'dokter_pengganti_id'=> \Yii::t('fe', 'Dokter Pengganti'),
                'status_kunjungan'=> \Yii::t('fe', 'Kunjungan'),
                'diagnosa'=> \Yii::t('fe', 'Jenis Pemeriksaan'),
                'referal' => \Yii::t('fe', 'Referral'),
                'jadwaldokter_id' => \Yii::t('fe', 'Jadwal Dokter'),
            ];
        }
    }

}
