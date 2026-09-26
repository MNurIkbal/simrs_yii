<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporankunjunganrj_v".
 *
 * @property int $pasien_id
 * @property string $no_identitas_pasien
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $statusrekammedis
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property int $propinsi_id
 * @property string $propinsi_nama
 * @property int $kabupaten_id
 * @property string $kabupaten_nama
 * @property int $kelurahan_id
 * @property string $kelurahan_nama
 * @property int $kecamatan_id
 * @property string $kecamatan_nama
 * @property int $pendaftaran_id
 * @property int $pekerjaan_id
 * @property string $pekerjaan_nama
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property bool $alih_status
 * @property bool $by_phone
 * @property bool $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property string $no_asuransi
 * @property string $namapemilik_asuransi
 * @property string $nopokokperusahaan
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $caramasuk_id
 * @property string $caramasuk_nama
 * @property int $shift_id
 * @property int $golonganumur_id
 * @property string $golonganumur_nama
 * @property string $no_rujukan
 * @property string $nama_perujuk
 * @property string $tanggal_rujukan
 * @property string $kodediagnosa_rujukan
 * @property int $asalrujukan_id
 * @property string $asalrujukan_nama
 * @property int $penanggungjawab_id
 * @property string $pengantar
 * @property string $hubungankeluarga
 * @property string $penanggungjawab_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $ruangan_singkatan
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $nama_pegawai
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property int $pegawai_id
 * @property string $tgl_renkontrol
 * @property int $pembayaranpelayanan_id
 * @property bool $panggil_antrian
 * @property int $antrian_id
 * @property string $tgl_antrian
 * @property string $no_antrian
 * @property bool $panggil_flag
 * @property int $loket_id
 * @property string $loket_nama
 * @property string $loket_fungsi
 * @property string $loket_singkatan
 * @property int $loket_nourut
 * @property string $loket_formatnomor
 * @property int $loket_maxantrian
 * @property string $nopeserta
 * @property string $tglcetakkartuasuransi
 * @property string $kodefeskestk1
 * @property string $nama_feskestk1
 * @property string $masaberlakukartu
 * @property string $nokartukeluarga
 * @property string $nopassport
 * @property bool $is_active
 * @property string $keterangan_pendaftaran
 * @property string $statusdok_rekammedik
 * @property int $kelompokpegawai_id
 * @property int $konsulpoli_id
 * @property bool $is_deleted
 * @property string $tglpasienpulang
 * @property string $jenis_kelamin
 * @property string $status_periksa
 * @property int $ruanganasal_id
 * @property string $ruanganasal_nama
 * @property string $jenisidentitas
 * @property string $namadepan
 * @property string $jeniskelamin
 * @property string $golongandarah
 * @property string $statusperkawinan
 * @property string $status_pasien
 * @property string $kunjungan
 * @property string $gelardepan
 * @property string $gelarbelakang_nama
 * @property string $rhesus
 * @property string $kondisikeluar_nama
 * @property string $carakeluar_nama
 * @property string $agama
 */
class LapDaftarPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporankunjunganrj_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kelurahan_id', 'kecamatan_id', 'pendaftaran_id', 'pekerjaan_id', 'carabayar_id', 'penjamin_id', 'caramasuk_id', 'shift_id', 'golonganumur_id', 'asalrujukan_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'pegawai_id', 'pembayaranpelayanan_id', 'antrian_id', 'loket_id', 'loket_nourut', 'loket_maxantrian', 'kelompokpegawai_id', 'konsulpoli_id', 'ruanganasal_id'], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kelurahan_id', 'kecamatan_id', 'pendaftaran_id', 'pekerjaan_id', 'carabayar_id', 'penjamin_id', 'caramasuk_id', 'shift_id', 'golonganumur_id', 'asalrujukan_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'pegawai_id', 'pembayaranpelayanan_id', 'antrian_id', 'loket_id', 'loket_nourut', 'loket_maxantrian', 'kelompokpegawai_id', 'konsulpoli_id', 'ruanganasal_id'], 'integer'],
            [['tanggal_lahir', 'tgl_rekam_medik', 'tgl_pendaftaran', 'tanggal_rujukan', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_antrian', 'tglcetakkartuasuransi', 'masaberlakukartu', 'tglpasienpulang'], 'safe'],
            [['alamat_pasien', 'loket_fungsi', 'keterangan_pendaftaran', 'ruanganasal_nama'], 'string'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'panggil_antrian', 'panggil_flag', 'is_active', 'is_deleted'], 'boolean'],
            [['no_identitas_pasien', 'nama_bin', 'umur'], 'string', 'max' => 30],
            [['nama_pasien', 'propinsi_nama', 'kabupaten_nama', 'kelurahan_nama', 'kecamatan_nama', 'pekerjaan_nama', 'transportasi', 'keadaan_masuk', 'status_masuk', 'no_asuransi', 'namapemilik_asuransi', 'nopokokperusahaan', 'carabayar_nama', 'penjamin_nama', 'caramasuk_nama', 'nama_perujuk', 'kodediagnosa_rujukan', 'asalrujukan_nama', 'pengantar', 'hubungankeluarga', 'penanggungjawab_nama', 'ruangan_nama', 'instalasi_nama', 'kelaspelayanan_nama', 'nama_pegawai', 'status_konfirmasi', 'loket_nama', 'nopeserta', 'kodefeskestk1', 'statusdok_rekammedik'], 'string', 'max' => 50],
            [['tempat_lahir', 'golonganumur_nama'], 'string', 'max' => 25],
            [['photopasien', 'nama_feskestk1', 'nopassport', 'jenis_kelamin', 'status_periksa', 'jenisidentitas', 'namadepan', 'jeniskelamin', 'golongandarah', 'statusperkawinan', 'status_pasien', 'kunjungan', 'gelardepan', 'rhesus', 'agama'], 'string', 'max' => 200],
            [['alamatemail', 'jeniskasuspenyakit_nama', 'nokartukeluarga', 'kondisikeluar_nama', 'carakeluar_nama'], 'string', 'max' => 100],
            [['statusrekammedis', 'no_rekam_medik'], 'string', 'max' => 10],
            [['no_pendaftaran', 'no_rujukan'], 'string', 'max' => 20],
            [['no_urutantri'], 'string', 'max' => 6],
            [['ruangan_singkatan'], 'string', 'max' => 3],
            [['no_antrian'], 'string', 'max' => 12],
            [['loket_singkatan'], 'string', 'max' => 1],
            [['loket_formatnomor'], 'string', 'max' => 5],
            [['gelarbelakang_nama'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'statusrekammedis' => 'Statusrekammedis',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'propinsi_id' => 'Propinsi ID',
            'propinsi_nama' => 'Propinsi Nama',
            'kabupaten_id' => 'Kabupaten ID',
            'kabupaten_nama' => 'Kabupaten Nama',
            'kelurahan_id' => 'Kelurahan ID',
            'kelurahan_nama' => 'Kelurahan Nama',
            'kecamatan_id' => 'Kecamatan ID',
            'kecamatan_nama' => 'Kecamatan Nama',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pekerjaan_id' => 'Pekerjaan ID',
            'pekerjaan_nama' => 'Pekerjaan Nama',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_urutantri' => 'No Urutantri',
            'transportasi' => 'Transportasi',
            'keadaan_masuk' => 'Keadaan Masuk',
            'alih_status' => 'Alih Status',
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => 'Kunjungan Rumah',
            'status_masuk' => 'Status Masuk',
            'umur' => 'Umur',
            'no_asuransi' => 'No Asuransi',
            'namapemilik_asuransi' => 'Namapemilik Asuransi',
            'nopokokperusahaan' => 'Nopokokperusahaan',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'caramasuk_id' => 'Caramasuk ID',
            'caramasuk_nama' => 'Caramasuk Nama',
            'shift_id' => 'Shift ID',
            'golonganumur_id' => 'Golonganumur ID',
            'golonganumur_nama' => 'Golonganumur Nama',
            'no_rujukan' => 'No Rujukan',
            'nama_perujuk' => 'Nama Perujuk',
            'tanggal_rujukan' => 'Tanggal Rujukan',
            'kodediagnosa_rujukan' => 'Kodediagnosa Rujukan',
            'asalrujukan_id' => 'Asalrujukan ID',
            'asalrujukan_nama' => 'Asalrujukan Nama',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'pengantar' => 'Pengantar',
            'hubungankeluarga' => 'Hubungankeluarga',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'ruangan_singkatan' => 'Ruangan Singkatan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'nama_pegawai' => 'Nama Pegawai',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'pegawai_id' => 'Pegawai ID',
            'tgl_renkontrol' => 'Tgl Renkontrol',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'panggil_antrian' => 'Panggil Antrian',
            'antrian_id' => 'Antrian ID',
            'tgl_antrian' => 'Tgl Antrian',
            'no_antrian' => 'No Antrian',
            'panggil_flag' => 'Panggil Flag',
            'loket_id' => 'Loket ID',
            'loket_nama' => 'Loket Nama',
            'loket_fungsi' => 'Loket Fungsi',
            'loket_singkatan' => 'Loket Singkatan',
            'loket_nourut' => 'Loket Nourut',
            'loket_formatnomor' => 'Loket Formatnomor',
            'loket_maxantrian' => 'Loket Maxantrian',
            'nopeserta' => 'Nopeserta',
            'tglcetakkartuasuransi' => 'Tglcetakkartuasuransi',
            'kodefeskestk1' => 'Kodefeskestk1',
            'nama_feskestk1' => 'Nama Feskestk1',
            'masaberlakukartu' => 'Masaberlakukartu',
            'nokartukeluarga' => 'Nokartukeluarga',
            'nopassport' => 'Nopassport',
            'is_active' => 'Is Active',
            'keterangan_pendaftaran' => 'Keterangan Pendaftaran',
            'statusdok_rekammedik' => 'Statusdok Rekammedik',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'is_deleted' => 'Is Deleted',
            'tglpasienpulang' => 'Tglpasienpulang',
            'jenis_kelamin' => 'Jenis Kelamin',
            'status_periksa' => 'Status Periksa',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruanganasal_nama' => 'Ruanganasal Nama',
            'jenisidentitas' => 'Jenisidentitas',
            'namadepan' => 'Namadepan',
            'jeniskelamin' => 'Jeniskelamin',
            'golongandarah' => 'Golongandarah',
            'statusperkawinan' => 'Statusperkawinan',
            'status_pasien' => 'Status Pasien',
            'kunjungan' => 'Kunjungan',
            'gelardepan' => 'Gelardepan',
            'gelarbelakang_nama' => 'Gelarbelakang Nama',
            'rhesus' => 'Rhesus',
            'kondisikeluar_nama' => 'Kondisikeluar Nama',
            'carakeluar_nama' => 'Carakeluar Nama',
            'agama' => 'Agama',
        ];
    }
}
