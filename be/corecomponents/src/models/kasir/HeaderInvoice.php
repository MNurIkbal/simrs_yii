<?php 

/**
 * ? @author Budi <budi@sirs.co.id>
 * ! @copyright Sirs
 */

namespace Doco\models\kasir;

use yii\base\Model;

class HeaderInvoice extends Model {
   
   public $no_pendaftaran;
   public $no_rekam_medik;
   public $nama_pasien;
   public $alamat;
   public $tanggal_lahir;
   public $jenis_kelamin;
   public $tgl_pembayaran;
   public $no_pembayaran;
   public $dok_pendaftaran;
   public $dok_ranap;
   public $r_pendaftaran;
   public $r_ranap;
   public $carabayar_nama;
   public $penjamin_nama;
   public $tgl_pendaftaran;
   public $kelaspelayanan_nama;
   public $kelas_ditagihkan_nama;
   public $biaya_administrasi;
   public $adm_resep;
   public $tgl_pasienpulang;
   public $pasienadmisi_id;
   public $umur;
   public $tgl_admisi;
   public $dokter_admisi;
   public $ruangan_nama;
   public $ruangan_titipan_nama;
   public $kamarruangan_nokamar;
   public $no_tempattidur;
   public $tgl_stopakomodasi;
   public $kelas_admisi;
   public $tagihan_rs;
   public $tgl_pulang;
   public $total_dijamin;
   public $kecamatan_nama;
   public $kelurahan_nama;
   public $kabupaten_nama;
   public $propinsi_nama;
   public $warganegara;
   public $alamat_pasien;
   public $kelaspelayanan_id;
   public $penjamin_id;

   /**
    * @return rules array default values
    */
   public function rules()
   {
      return [
         [[
               'no_pendaftaran',
               'no_rekam_medik',
               'nama_pasien',
               'alamat',
               'tanggal_lahir',
               'jenis_kelamin',
               'tgl_pembayaran',
               'no_pembayaran',
               'dok_pendaftaran',
               'dok_ranap',
               'r_pendaftaran',
               'r_ranap',
               'carabayar_nama',
               'penjamin_nama',
               'tgl_pendaftaran',
               'kelaspelayanan_nama',
               'kelas_ditagihkan_nama',
               'biaya_administrasi',
               'adm_resep',
               'tgl_pasienpulang',
               'pasienadmisi_id',
               'umur',
               'tgl_admisi',
               'dokter_admisi',
               'ruangan_nama',
               'ruangan_titipan_nama',
               'kamarruangan_nokamar',
               'no_tempattidur',
               'tgl_stopakomodasi',
               'kelas_admisi',
               'tagihan_rs',
               'tgl_pulang',
               'total_dijamin',
               'kecamatan_nama',
               'kelurahan_nama',
               'kabupaten_nama',
               'propinsi_nama',
               'warganegara',
               'kelaspelayanan_id',
               'penjamin_id',
         ], 'default', 'value' => null],
         [[
               'no_pendaftaran',
               'no_rekam_medik',
               'nama_pasien',
               'alamat',
               'tanggal_lahir',
               'jenis_kelamin',
               'tgl_pembayaran',
               'no_pembayaran',
               'dok_pendaftaran',
               'dok_ranap',
               'r_pendaftaran',
               'r_ranap',
               'carabayar_nama',
               'penjamin_nama',
               'tgl_pendaftaran',
               'kelaspelayanan_nama',
               'kelas_ditagihkan_nama',
               'biaya_administrasi',
               'adm_resep',
               'tgl_pasienpulang',
               'pasienadmisi_id',
               'umur',
               'tgl_admisi',
               'dokter_admisi',
               'ruangan_nama',
               'ruangan_titipan_nama',
               'kamarruangan_nokamar',
               'no_tempattidur',
               'tgl_stopakomodasi',
               'kelas_admisi',
               'tagihan_rs',
               'tgl_pulang',
               'total_dijamin',
               'kecamatan_nama',
               'kelurahan_nama',
               'kabupaten_nama',
               'propinsi_nama',
               'warganegara',
               'alamat_pasien',
         ], 'safe'],
      ];
   }
}