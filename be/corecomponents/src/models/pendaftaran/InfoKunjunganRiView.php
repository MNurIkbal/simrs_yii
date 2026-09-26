<?php

namespace Doco\models\pendaftaran;

use Yii;

/**
 * This is the model class for table "infokunjunganri_v".
 *
 * @property int $pasien_id
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
 * @property string $agama
 * @property string $golongandarah
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $statusrekammedis
 * @property string $statusperkawinan
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_pasien
 * @property bool $alih_status
 * @property bool $by_phone
 * @property bool $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property int $golonganumur_id
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
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $pasienadmisi_id
 * @property string $tgl_admisi
 * @property string $tgl_pulang
 * @property string $kunjungan
 * @property bool $status_keluar
 * @property bool $rawat_gabung
 * @property int $kamarruangan_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property int $pegawai_id
 * @property string $rhesus
 * @property int $anakke
 * @property int $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property int $suku_id
 * @property string $suku_nama
 * @property int $pendidikan_id
 * @property string $pendidikan_nama
 * @property string $nama_ibu
 * @property string $nama_ayah
 * @property string $nopeserta
 * @property string $tglcetakkartuasuransi
 * @property string $kodefeskestk1
 * @property string $nama_feskestk1
 * @property string $masaberlakukartu
 * @property string $nokartukeluarga
 * @property string $nopassport
 * @property bool $is_active
 * @property string $keterangan_pendaftaran
 * @property int $kelompokpegawai_id
 * @property bool $is_deleted
 * @property string $status_periksa
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $golonganumur_nama
 * @property string $jenis_kelamin
 */
class InfoKunjunganRiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infokunjunganri_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'pendaftaran_id', 'golonganumur_id', 'carabayar_id', 'penjamin_id', 'caramasuk_id', 'shift_id', 'asalrujukan_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'pasienadmisi_id', 'kamarruangan_id', 'pegawai_id', 'anakke', 'jumlah_bersaudara', 'suku_id', 'pendidikan_id', 'kelompokpegawai_id'], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'pendaftaran_id', 'golonganumur_id', 'carabayar_id', 'penjamin_id', 'caramasuk_id', 'shift_id', 'asalrujukan_id', 'penanggungjawab_id', 'ruangan_id', 'instalasi_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'pasienadmisi_id', 'kamarruangan_id', 'pegawai_id', 'anakke', 'jumlah_bersaudara', 'suku_id', 'pendidikan_id', 'kelompokpegawai_id'], 'integer'],
            [['tanggal_lahir', 'tgl_rekam_medik', 'tgl_pendaftaran', 'tanggal_rujukan', 'tgl_admisi', 'tgl_pulang', 'tgl_konfirmasi', 'tglcetakkartuasuransi', 'masaberlakukartu'], 'safe'],
            [['alamat_pasien', 'keterangan_pendaftaran'], 'string'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'status_keluar', 'rawat_gabung', 'is_active', 'is_deleted'], 'boolean'],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'agama', 'statusperkawinan', 'no_pendaftaran', 'no_rujukan', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin', 'umur'], 'string', 'max' => 30],
            [['nama_pasien', 'transportasi', 'keadaan_masuk', 'status_pasien', 'status_masuk', 'no_asuransi', 'namapemilik_asuransi', 'nopokokperusahaan', 'carabayar_nama', 'penjamin_nama', 'caramasuk_nama', 'nama_perujuk', 'kodediagnosa_rujukan', 'asalrujukan_nama', 'pengantar', 'hubungankeluarga', 'penanggungjawab_nama', 'ruangan_nama', 'instalasi_nama', 'kelaspelayanan_nama', 'kunjungan', 'nama_pegawai', 'status_konfirmasi', 'suku_nama', 'pendidikan_nama', 'nama_ibu', 'nama_ayah', 'nopeserta', 'kodefeskestk1'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara', 'kamarruangan_nokamar', 'golonganumur_nama'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopasien', 'nama_feskestk1', 'nopassport', 'status_periksa', 'jenis_kelamin'], 'string', 'max' => 200],
            [['alamatemail', 'jeniskasuspenyakit_nama', 'nokartukeluarga'], 'string', 'max' => 100],
            [['statusrekammedis', 'no_rekam_medik', 'gelardepan'], 'string', 'max' => 10],
            [['no_urutantri'], 'string', 'max' => 6],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['no_tempattidur'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
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
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'statusrekammedis' => 'Statusrekammedis',
            'statusperkawinan' => 'Statusperkawinan',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_urutantri' => 'No Urutantri',
            'transportasi' => 'Transportasi',
            'keadaan_masuk' => 'Keadaan Masuk',
            'status_pasien' => 'Status Pasien',
            'alih_status' => 'Alih Status',
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => 'Kunjungan Rumah',
            'status_masuk' => 'Status Masuk',
            'umur' => 'Umur',
            'golonganumur_id' => 'Golonganumur ID',
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
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_admisi' => 'Tgl Admisi',
            'tgl_pulang' => 'Tgl Pulang',
            'kunjungan' => 'Kunjungan',
            'status_keluar' => 'Status Keluar',
            'rawat_gabung' => 'Rawat Gabung',
            'kamarruangan_id' => 'Kamarruangan ID',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'pegawai_id' => 'Pegawai ID',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'warga_negara' => 'Warga Negara',
            'suku_id' => 'Suku ID',
            'suku_nama' => 'Suku Nama',
            'pendidikan_id' => 'Pendidikan ID',
            'pendidikan_nama' => 'Pendidikan Nama',
            'nama_ibu' => 'Nama Ibu',
            'nama_ayah' => 'Nama Ayah',
            'nopeserta' => 'Nopeserta',
            'tglcetakkartuasuransi' => 'Tglcetakkartuasuransi',
            'kodefeskestk1' => 'Kodefeskestk1',
            'nama_feskestk1' => 'Nama Feskestk1',
            'masaberlakukartu' => 'Masaberlakukartu',
            'nokartukeluarga' => 'Nokartukeluarga',
            'nopassport' => 'Nopassport',
            'is_active' => 'Is Active',
            'keterangan_pendaftaran' => 'Keterangan Pendaftaran',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'is_deleted' => 'Is Deleted',
            'status_periksa' => 'Status Periksa',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'golonganumur_nama' => 'Golonganumur Nama',
            'jenis_kelamin' => 'Jenis Kelamin',
        ];
    }
}
