<?php

namespace Doco\models\pendaftaran;

use Yii;

/**
 * This is the model class for table "pasiencetakan_v".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 * @property string $jenisidentitas
 * @property string $identitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_depan
 * @property string $nama_bin
 * @property string $tempat_lahir
 * @property string $jeniskelamin
 * @property string $jenis_kelamin
 * @property string $statusperkawinan
 * @property string $status_perkawinan
 * @property string $nama_ibu
 * @property string $alamat_sekarang
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property int $propinsi_id
 * @property string $propinsi_nama
 * @property int $kabupaten_id
 * @property string $kabupaten_nama
 * @property int $kecamatan_id
 * @property string $kecamatan_nama
 * @property int $kelurahan_id
 * @property string $kelurahan_nama
 * @property string $no_mobile_pasien
 * @property string $no_telepon_pasien
 * @property int $pekerjaan_id
 * @property string $pekerjaan_nama
 * @property string $warga_negara
 * @property string $warganegara
 * @property string $agama
 * @property string $agama_pasien
 * @property string $alamatemail
 * @property int $suku_id
 * @property string $suku_nama
 * @property string $nama_ayah
 * @property int $anakke
 * @property int $jumlah_bersaudara
 * @property string $golongandarah
 * @property string $golongan_darah
 * @property string $photopasien
 * @property bool $is_aps
 * @property int $dokrekammedis_id
 * @property int $pendidikan_id
 * @property string $pendidikan_nama
 * @property string $additional_pasien
 * @property string $catatanpenting_pasien
 * @property string $alergi
 * @property string $penanggungjawab_nama
 * @property string $hubungankeluarga
 * @property string $penanggungjawab_alamat
 * @property string $penanggungjawab_kelurahan
 * @property string $penanggungjawab_kecamatan
 * @property string $penanggungjawab_alamat
 * @property string $penanggungjawab_notelp
 * @property string $nokartuasuransi
 * @property string $kode_pos
 * @property string $tgl_update_terakhir
 * @property string $petugas_nama
 * @property string $tgl_pembuatan
 * @property string $pembuat_nama
 * @property string $pengirim
 * @property string $instansi
 * @property string $diagnosa_masuk
 * @property string $dipindahkanke
 * @property string $tanggal
 * @property string $ruangan
 * @property string $kelas
 * @property string $kelas_diminta
 * @property string $dokter
 * @property string $tgl_keluar
 * @property string $jam_keluar
 * @property string $kodeicd_kodetindakan
 * @property string $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $keluarga_nama
 * @property string $penanggungbiaya_alamat
 * @property string $penanggungbiaya_kelurahan
 * @property string $penanggungbiaya_kecamatan
 * @property string $penanggungbiaya_pekerjaan
 * @property string $penanggungbiaya_telepon
 * @property string $tgl_pendaftaran
 * @property string $umur
 */
class SypasiencetakanV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_pasiencetakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pendidikan_id', 'pendaftaran_id', 'penjamin_id',], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pendidikan_id', 'pendaftaran_id', 'penjamin_id',], 'integer'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_update_terakhir', 'tgl_pembuatan', 'penanggungjawab_kelurahan', 'penanggungjawab_kecamatan', 'pengirim', 'instansi', 'diagnosa_masuk', 'dipindahkanke', 'tanggal', 'ruangan', 'kelas', 'kelas_diminta', 'dokter', 'tgl_keluar', 'jam_keluar', 'kodeicd_kodetindakan', 'no_pendaftaran',  'penanggungbiaya_alamat', 'penanggungbiaya_kelurahan', 'penanggungbiaya_kecamatan', 'penanggungbiaya_pekerjaan','penanggungbiaya_telepon', 'keluarga_nama', 'tgl_pendaftaran', 'umur', 'penanggungbiaya_nama'], 'safe'],
            [['identitas', 'nama_depan', 'jenis_kelamin', 'status_perkawinan', 'alamat_sekarang', 'alamat_pasien', 'propinsi_nama', 'kabupaten_nama', 'kecamatan_nama', 'kelurahan_nama', 'warganegara', 'agama_pasien', 'golongan_darah', 'additional_pasien', 'catatanpenting_pasien', 'alergi', 'penanggungjawab_nama', 'hubungankeluarga', 'penanggungjawab_alamat', 'penanggungjawab_kelurahan', 'penanggungjawab_kecamatan', 'penanggungjawab_notelp', 'nokartuasuransi', 'kode_pos', 'pengirim', 'instansi', 'diagnosa_masuk', 'dipindahkanke', 'tanggal', 'ruangan', 'kelas', 'kelas_diminta', 'dokter', 'tgl_keluar', 'jam_keluar', 'kodeicd_kodetindakan', 'no_pendaftaran', 'penanggungbiaya_alamat', 'penanggungbiaya_kelurahan', 'penanggungbiaya_kecamatan', 'penanggungbiaya_pekerjaan', 'penanggungbiaya_telepon', 'penjamin_nama', 'penanggungbiaya_nama'], 'string'],
            [['is_aps'], 'boolean'],
            [['no_rekam_medik', 'alamatemail'], 'string', 'max' => 100],
            [['nama_pasien', 'nama_ibu', 'pekerjaan_nama', 'suku_nama', 'nama_ayah', 'pendidikan_nama', 'petugas_nama', 'pembuat_nama'], 'string', 'max' => 50],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'no_mobile_pasien', 'agama'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin'], 'string', 'max' => 30],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['golongandarah'], 'string', 'max' => 10],
            [['photopasien'], 'string', 'max' => 200],
            // [['Pengirim', 'penanggungjawabtera_hubungan'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenisidentitas' => 'Jenisidentitas',
            'identitas' => 'Identitas',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'namadepan' => 'Namadepan',
            'nama_depan' => 'Nama Depan',
            'nama_bin' => 'Nama Bin',
            'tempat_lahir' => 'Tempat Lahir',
            'jeniskelamin' => 'Jeniskelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
            'statusperkawinan' => 'Statusperkawinan',
            'status_perkawinan' => 'Status Perkawinan',
            'nama_ibu' => 'Nama Ibu',
            'alamat_sekarang' => 'Alamat Sekarang',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'propinsi_id' => 'Propinsi ID',
            'propinsi_nama' => 'Propinsi Nama',
            'kabupaten_id' => 'Kabupaten ID',
            'kabupaten_nama' => 'Kabupaten Nama',
            'kecamatan_id' => 'Kecamatan ID',
            'kecamatan_nama' => 'Kecamatan Nama',
            'kelurahan_id' => 'Kelurahan ID',
            'kelurahan_nama' => 'Kelurahan Nama',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'pekerjaan_id' => 'Pekerjaan ID',
            'pekerjaan_nama' => 'Pekerjaan Nama',
            'warga_negara' => 'Warga Negara',
            'warganegara' => 'Warganegara',
            'agama' => 'Agama',
            'agama_pasien' => 'Agama Pasien',
            'alamatemail' => 'Alamatemail',
            'suku_id' => 'Suku ID',
            'suku_nama' => 'Suku Nama',
            'nama_ayah' => 'Nama Ayah',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'golongandarah' => 'Golongandarah',
            'golongan_darah' => 'Golongan Darah',
            'photopasien' => 'Photopasien',
            'is_aps' => 'Is Aps',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'pendidikan_id' => 'Pendidikan ID',
            'pendidikan_nama' => 'Pendidikan Nama',
            'additional_pasien' => 'Additional Pasien',
            'catatanpenting_pasien' => 'Catatanpenting Pasien',
            'alergi' => 'Alergi',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'hubungankeluarga' => 'Hubungankeluarga',
            'penanggungjawab_alamat' => 'Penanggungjawab Alamat',
            'penanggungjawab_kelurahan' => 'Penanggung Jawab Kelurahan',
            'penanggungjawab_kecamatan' => 'Penanggung Jawab Kecamatan',
            'penanggungjawab_notelp' => 'Penanggungjawab Notelp',
            'nokartuasuransi' => 'Nokartuasuransi',
            'kode_pos' => 'Kode Pos',
            'tgl_update_terakhir' => 'Tgl Update Terakhir',
            'petugas_nama' => 'Petugas Nama',
            'tgl_pembuatan' => 'Tgl Pembuatan',
            'pembuat_nama' => 'Pembuat Nama',
            // 3 ini
            'pengirim' => 'Pengirim',
            'instansi' => 'Instansi',
            'dipindahkanke' => 'Dipindahkan ke',
            'tanggal' => 'Tanggal',
            'ruangan' => 'Ruangan',
            'kelas' => 'Kelas',
            'kelas_diminta' => 'Kelas diminta',
            'dokter' => 'Dokter',
            'tgl_keluar' => 'Tanggal Keluar',
            'jam_keluar' => 'Jam Keluar',
            'kodeicd_kodetindakan' => 'Kode Icd/ Kode Tindakan',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'Nomor Registrasi',
            'keluarga_nama' => 'Penanggung Biaya Nama',
            'penanggungbiaya_alamat' => 'Penanggung Biaya Alamat',
            'penanggungbiaya_kelurahan' => 'Penanggung Biaya Kelurahan',
            'penanggungbiaya_kecamatan' => 'Penanggung Biaya Kecamatan',
            'penanggungbiaya_pekerjaan' => 'Penanggung Biaya Pekerjaan',
            'penanggungbiaya_notelp' => 'Penanggung Biaya Telepon',
            'tgl_pendaftaran' => 'Tanggal Pendaftaran',
            'umur' => 'Umur',
            'identitas' => 'Identitas',
            'no_identitas_pasien' => "Nomor Identitas",
            'diagnosa_masuk' => "Diagnosa Masuk",
        ];
    }
}
