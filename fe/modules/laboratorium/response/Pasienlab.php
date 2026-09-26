<?php
namespace app\modules\laboratorium\response;

use Yii;

class Pasienlab extends \yii\base\Model
{
    public $pasienkirimkeunitlain_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $no_pendaftaran;
    public $tgl_rujukan;
    public $no_rujukan;
    public $pasien_id;
    public $no_rekam_medik;
    public $nama_pasien;
    public $umur;
    public $jenis_kelamin;
    public $kelaspelayanan_nama;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_nama;
    public $kamarruangan_nokamar;
    public $no_tempattidur;
    public $pegawai_id;
    public $dokter_perujuk;
    public $carabayar_id;
    public $carabayar_nama;
    public $penjamin_id;
    public $penjamin_nama;
    public $status_penunjang;
    public $stat_penunjang;
    public $kelaspelayanan_id;
    public $jeniskasuspenyakit_id;
    public $ruangan_id;
    public $tgl_pendaftaran;
    public $kunjungan;
    public $ruanganpenunjang_id;
    public $tanggal_lahir;
    public $status_pasien;
    public $groupcarabayar_id;
    public $instalasipen_id;
    public $is_bayar;
    public $status_periksa;
    public $catatan_dokterpengirim;
    public $no_masukpenunjang;
    
    public function rules()
    {
         return [
            [[
                "pasienkirimkeunitlain_id",
                "pendaftaran_id",
                "pasienadmisi_id",
                "no_pendaftaran",
                "tgl_rujukan",
                "no_rujukan",
                "pasien_id",
                "no_rekam_medik",
                "nama_pasien",
                "umur",
                "jenis_kelamin",
                "kelaspelayanan_nama",
                "instalasi_id",
                "instalasi_nama",
                "ruangan_nama",
                "kamarruangan_nokamar",
                "no_tempattidur",
                "pegawai_id",
                "dokter_perujuk",
                "carabayar_id",
                "carabayar_nama",
                "penjamin_id",
                "penjamin_nama",
                "status_penunjang",
                "stat_penunjang",
                "kelaspelayanan_id",
                "jeniskasuspenyakit_id",
                "ruangan_id",
                "tgl_pendaftaran",
                "kunjungan",
                "ruanganpenunjang_id",
                "tanggal_lahir",
                "status_pasien",
                "groupcarabayar_id",
                "instalasipen_id",
                "is_bayar",
                "status_periksa",
                "catatan_dokterpengirim",
                'no_masukpenunjang'
            ], 'safe'],
        ];
    }
}
