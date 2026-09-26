<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforinciantagihapasien_v".
 *
 * @property int $profilrs_id
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property int $anakke
 * @property int $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $photopasien
 * @property string $alamatemail
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $umur
 * @property string $no_asuransi
 * @property string $namapemilik_asuransi
 * @property string $nopokokperusahaan
 * @property string $namaperusahaan
 * @property string $tgl_selesaiperiksa
 * @property int $tindakanpelayanan_id
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $tgl_tindakan
 * @property int $daftartindakan_id
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property bool $daftartindakan_karcis
 * @property bool $daftartindakan_visite
 * @property bool $daftartindakan_konsul
 * @property double $tarif_rsakomodasi
 * @property double $tarif_medis
 * @property double $tarif_paramedis
 * @property double $tarif_bhp
 * @property double $tarif_satuan
 * @property double $tarif_tindakan
 * @property string $satuan_tindakan
 * @property double $qty_tindakan
 * @property bool $cyto_tindakan
 * @property double $tarifcyto_tindakan
 * @property double $discount_tindakan
 * @property double $pembebasan_tindakan
 * @property double $subsidiasuransi_tindakan
 * @property double $subsidipemerintah_tindakan
 * @property double $subsisidirumahsakit_tindakan
 * @property double $uangditerima_tindakan
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $pembayaranpelayanan_id
 * @property int $kategoritindakan_id
 * @property string $kategoritindakan_nama
 * @property int $pegawai_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelar_belakang
 * @property int $ruanganpendaftaran_id
 * @property int $tindakansudahbayar_id
 * @property double $biayaservice
 * @property double $biayaadministrasi
 * @property double $biayakonseling
 * @property bool $is_alkes
 * @property string $tglcetakkartuasuransi
 * @property string $kodefeskestk1
 * @property string $nama_feskestk1
 * @property string $masaberlakukartu
 * @property string $nokartukeluarga
 * @property string $nopassport
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property bool $is_active
 */
class InforinciantagihapasienView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inforinciantagihapasien_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profilrs_id', 'pasien_id', 'rt', 'rw', 'anakke', 'jumlah_bersaudara', 'pendaftaran_id', 'tindakanpelayanan_id', 'penjamin_id', 'carabayar_id', 'kelaspelayanan_id', 'instalasi_id', 'ruangan_id', 'daftartindakan_id', 'tipepaket_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kategoritindakan_id', 'pegawai_id', 'ruanganpendaftaran_id', 'tindakansudahbayar_id'], 'default', 'value' => null],
            [['profilrs_id', 'pasien_id', 'rt', 'rw', 'anakke', 'jumlah_bersaudara', 'pendaftaran_id', 'tindakanpelayanan_id', 'penjamin_id', 'carabayar_id', 'kelaspelayanan_id', 'instalasi_id', 'ruangan_id', 'daftartindakan_id', 'tipepaket_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kategoritindakan_id', 'pegawai_id', 'ruanganpendaftaran_id', 'tindakansudahbayar_id'], 'integer'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_tindakan', 'tglcetakkartuasuransi', 'masaberlakukartu', 'tgl_konfirmasi'], 'safe'],
            [['alamat_pasien', 'daftartindakan_kode', 'daftartindakan_nama', 'satuan_tindakan', 'kategoritindakan_nama'], 'string'],
            [['daftartindakan_karcis', 'daftartindakan_visite', 'daftartindakan_konsul', 'cyto_tindakan', 'is_alkes', 'is_active'], 'boolean'],
            [['tarif_rsakomodasi', 'tarif_medis', 'tarif_paramedis', 'tarif_bhp', 'tarif_satuan', 'tarif_tindakan', 'qty_tindakan', 'tarifcyto_tindakan', 'discount_tindakan', 'pembebasan_tindakan', 'subsidiasuransi_tindakan', 'subsidipemerintah_tindakan', 'subsisidirumahsakit_tindakan', 'uangditerima_tindakan', 'biayaservice', 'biayaadministrasi', 'biayakonseling'], 'number'],
            [['no_rekam_medik', 'gelardepan'], 'string', 'max' => 10],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien', 'no_pendaftaran'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin', 'umur'], 'string', 'max' => 30],
            [['nama_pasien', 'no_asuransi', 'namapemilik_asuransi', 'nopokokperusahaan', 'namaperusahaan', 'penjamin_nama', 'carabayar_nama', 'kelaspelayanan_nama', 'instalasi_nama', 'ruangan_nama', 'tipepaket_nama', 'nama_pegawai', 'kodefeskestk1', 'status_konfirmasi'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien', 'gelar_belakang', 'nama_feskestk1', 'nopassport'], 'string', 'max' => 200],
            [['alamatemail', 'jeniskasuspenyakit_nama', 'nokartukeluarga'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profilrs_id' => 'Profilrs ID',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'jenisidentitas' => 'Jenisidentitas',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'statusperkawinan' => 'Statusperkawinan',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'warga_negara' => 'Warga Negara',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'umur' => 'Umur',
            'no_asuransi' => 'No Asuransi',
            'namapemilik_asuransi' => 'Namapemilik Asuransi',
            'nopokokperusahaan' => 'Nopokokperusahaan',
            'namaperusahaan' => 'Namaperusahaan',
            'tgl_selesaiperiksa' => 'Tgl Selesaiperiksa',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'tgl_tindakan' => 'Tgl Tindakan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'daftartindakan_karcis' => 'Daftartindakan Karcis',
            'daftartindakan_visite' => 'Daftartindakan Visite',
            'daftartindakan_konsul' => 'Daftartindakan Konsul',
            'tarif_rsakomodasi' => 'Tarif Rsakomodasi',
            'tarif_medis' => 'Tarif Medis',
            'tarif_paramedis' => 'Tarif Paramedis',
            'tarif_bhp' => 'Tarif Bhp',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_tindakan' => 'Tarif Tindakan',
            'satuan_tindakan' => 'Satuan Tindakan',
            'qty_tindakan' => 'Qty Tindakan',
            'cyto_tindakan' => 'Cyto Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'discount_tindakan' => 'Discount Tindakan',
            'pembebasan_tindakan' => 'Pembebasan Tindakan',
            'subsidiasuransi_tindakan' => 'Subsidiasuransi Tindakan',
            'subsidipemerintah_tindakan' => 'Subsidipemerintah Tindakan',
            'subsisidirumahsakit_tindakan' => 'Subsisidirumahsakit Tindakan',
            'uangditerima_tindakan' => 'Uangditerima Tindakan',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kategoritindakan_nama' => 'Kategoritindakan Nama',
            'pegawai_id' => 'Pegawai ID',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelar_belakang' => 'Gelar Belakang',
            'ruanganpendaftaran_id' => 'Ruanganpendaftaran ID',
            'tindakansudahbayar_id' => 'Tindakansudahbayar ID',
            'biayaservice' => 'Biayaservice',
            'biayaadministrasi' => 'Biayaadministrasi',
            'biayakonseling' => 'Biayakonseling',
            'is_alkes' => 'Is Alkes',
            'tglcetakkartuasuransi' => 'Tglcetakkartuasuransi',
            'kodefeskestk1' => 'Kodefeskestk1',
            'nama_feskestk1' => 'Nama Feskestk1',
            'masaberlakukartu' => 'Masaberlakukartu',
            'nokartukeluarga' => 'Nokartukeluarga',
            'nopassport' => 'Nopassport',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'is_active' => 'Is Active',
        ];
    }
}
