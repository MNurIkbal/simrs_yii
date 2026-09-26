<?php

namespace app\modules\v1\models;

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
 * @property string $nopeserta_bpjs
 * @property int $pendidikan_id
 * @property string $pendidikan_nama
 * @property double $total_sisapiutang
 * @property string $additional_pasien
 * @property string $catatanpenting_pasien
 * @property string $alergi
 * @property string $penanggungjawab_nama
 * @property string $hubungankeluarga
 * @property string $penanggungjawab_alamat
 * @property string $penanggungjawab_notelp
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $nokartuasuransi
 * @property string $kode_pos
 * @property string $tgl_update_terakhir
 * @property string $petugas_nama
 * @property string $tgl_pembuatan
 * @property string $pembuat_nama
 * @property string $penanggungjawabtera_nama
 * @property string $penanggungjawabtera_hubungan
 * @property string $penanggungjawabtera_alamat
 */
class PasiencetakanV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasiencetakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pendidikan_id'], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pendidikan_id'], 'integer'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_update_terakhir', 'tgl_pembuatan'], 'safe'],
            [['identitas', 'nama_depan', 'jenis_kelamin', 'status_perkawinan', 'alamat_sekarang', 'alamat_pasien', 'propinsi_nama', 'kabupaten_nama', 'kecamatan_nama', 'kelurahan_nama', 'warganegara', 'agama_pasien', 'golongan_darah', 'nopeserta_bpjs', 'additional_pasien', 'catatanpenting_pasien', 'alergi', 'penanggungjawab_nama', 'hubungankeluarga', 'penanggungjawab_alamat', 'penanggungjawab_notelp', 'carabayar_nama', 'penjamin_nama', 'nokartuasuransi', 'kode_pos', 'penanggungjawabtera_alamat'], 'string'],
            [['is_aps'], 'boolean'],
            [['total_sisapiutang'], 'number'],
            [['no_rekam_medik', 'alamatemail'], 'string', 'max' => 100],
            [['nama_pasien', 'nama_ibu', 'pekerjaan_nama', 'suku_nama', 'nama_ayah', 'pendidikan_nama', 'petugas_nama', 'pembuat_nama'], 'string', 'max' => 50],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'no_mobile_pasien', 'agama'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin'], 'string', 'max' => 30],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['golongandarah'], 'string', 'max' => 10],
            [['photopasien'], 'string', 'max' => 200],
            [['penanggungjawabtera_nama', 'penanggungjawabtera_hubungan'], 'string', 'max' => 150],
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
            'nopeserta_bpjs' => 'Nopeserta Bpjs',
            'pendidikan_id' => 'Pendidikan ID',
            'pendidikan_nama' => 'Pendidikan Nama',
            'total_sisapiutang' => 'Total Sisapiutang',
            'additional_pasien' => 'Additional Pasien',
            'catatanpenting_pasien' => 'Catatanpenting Pasien',
            'alergi' => 'Alergi',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'hubungankeluarga' => 'Hubungankeluarga',
            'penanggungjawab_alamat' => 'Penanggungjawab Alamat',
            'penanggungjawab_notelp' => 'Penanggungjawab Notelp',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'nokartuasuransi' => 'Nokartuasuransi',
            'kode_pos' => 'Kode Pos',
            'tgl_update_terakhir' => 'Tgl Update Terakhir',
            'petugas_nama' => 'Petugas Nama',
            'tgl_pembuatan' => 'Tgl Pembuatan',
            'pembuat_nama' => 'Pembuat Nama',
            'penanggungjawabtera_nama' => 'Penanggungjawabtera Nama',
            'penanggungjawabtera_hubungan' => 'Penanggungjawabtera Hubungan',
            'penanggungjawabtera_alamat' => 'Penanggungjawabtera Alamat',
        ];
    }
}
