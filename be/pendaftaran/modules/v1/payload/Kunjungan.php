<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class Kunjungan extends \Doco\components\DocoBaseModel
{
    public $dokter_id;
    public $pegawai_id;
    public $jeniskasuspenyakit_id;
    public $keadaan_masuk;
    public $kelaspelayanan_id;
    public $keterangan;
    public $instalasi_id;
    public $ruangan_id;
    public $tgl_pendaftaran;
    public $tindakan_karcis;
    public $list_paket;
    public $transportasi;
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
    public $list_penunjang;
    public $is_ranap;
    public $is_skd;
    public $limit_tagihan;
    public $nomor_urut;
    public $no_exportexcel;
    public $dokterpengirim_id;
    public $styrujukaninstalasi_id;
    public $waktu_kunjungan;
    public $dokter_pengganti_id;
    public $status_kunjungan;
    public $diagnosa;
    public $referal;
    public $jadwaldokter_id;

    protected $xssProtected = [
        'dokter_id',
        'pegawai_id',
        'jeniskasuspenyakit_id',
        'keadaan_masuk',
        'kelaspelayanan_id',
        'keterangan',
        'ruangan_id',
        'tgl_pendaftaran',
        'tindakan_karcis',
        'transportasi',
        'list_penunjang',
        'instalasi_id',
        'is_ranap',
        'list_paket',
        'is_skd',
        'limit_tagihan',
        'nomor_urut'
    ];

    public function rules()
    {
         return [
            [[
                'pegawai_id',
                'dokter_id',
                'jeniskasuspenyakit_id',
                'keadaan_masuk',
                'kelaspelayanan_id',
                'keterangan',
                'ruangan_id',
                'tgl_pendaftaran',
                'tindakan_karcis',
                'transportasi',
                'catatanpenting_pasien',
                'namadepan',
                'nama_pasien',
                'propinsi_id',
                'kabupaten_id',
                'kecamatan_id',
                'kelurahan_id',
                'rt',
                'rw',
                'kode_pos',
                'alamat_pasien',
                'no_telepon_pasien',
                'pekerjaan_id',
                'pt',
                'list_penunjang',
                'instalasi_id',
                'is_ranap',
                'list_paket',
                'is_skd',
                'limit_tagihan',
                'nomor_urut',
                'no_exportexcel',
                'dokterpengirim_id',
                'styrujukaninstalasi_id',
                'dokter_pengganti_id',
                'waktu_kunjungan',
                'status_kunjungan',
                'diagnosa',
                'referal',
                'jadwaldokter_id',
            ],'safe'],
            [[
                'jeniskasuspenyakit_id',
                'kelaspelayanan_id',
                'ruangan_id',
            ],'required', 'on' => 'pendaftaran-rajal'],
            [[
                'jeniskasuspenyakit_id',
                'kelaspelayanan_id',
                'ruangan_id',
                'nomor_urut'
            ],'required', 'on' => 'pendaftaran-rajal-nomor-urut'],
            // [['keterangan'], 'keteranganRules'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Dokter',
            'dokter_id' => 'Dokter',
            'jeniskasuspenyakit_id' => 'Jenis kasus penyakit',
            'keadaan_masuk'=> 'Keadaan masuk',
            'kelaspelayanan_id' => 'Kelas pelayanan',
            'keterangan'=> 'Keterangan pendaftaran',
            'ruangan_id'=> 'Ruangan',
            'tgl_pendaftaran'=> 'Tanggal Pendaftaran',
            'transportasi'=> 'Transportasi',
            'is_skd'=> 'Surat Keterangan Dokter',
            'limit_tagihan' => 'Limit Tagihan',
            'namadepan'=> 'Nama Depan',
            'nama_pasien'=> 'Nama Pasien',
            'propinsi_id'=> 'Propinsi',
            'kabupaten_id'=> 'Kabupaten',
            'kecamatan_id'=> 'Kecamatan',
            'kelurahan_id'=> 'Kelurahan',
            'rt'=> 'RT',
            'rw'=> 'RW',
            'kode_pos'=> 'Kode Pos',
            'alamat_pasien'=> 'Alamat Pasien',
            'no_telepon_pasien'=> 'No Telepon Pasien',
            'pekerjaan_id'=> 'pekerjaan_id',
            'pt'=> 'PT',
            'dokterpengirim_id' => 'Dokter Pengirim',
            'styrujukaninstalasi_id' => 'Rujukan Dari',
            'diagnosa' => 'Diagnosa',
            'referal' => 'Referal',
            'jadwaldokter_id' => 'Jadwal Dokter'
        ];
    }

    public function keteranganRules()
    {
        if (isset($this->is_ranap) && !$this->is_ranap && empty($this->keterangan)) {
            $this->addError('keterangan', 'Keterangan Pendaftaran tidak boleh kosong');
        }
    }

}
