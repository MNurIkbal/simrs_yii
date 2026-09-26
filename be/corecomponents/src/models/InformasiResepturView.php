<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "informasireseptur_v".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $reseptur_id
 * @property string $tglreseptur
 * @property string $noreseptur
 * @property integer $instalasireseptur_id
 * @property string $instalasireseptur_nama
 * @property integer $ruanganreseptur_id
 * @property string $ruanganreseptur_nama
 * @property string $fileresep
 * @property integer $pasien_id
 * @property string $no_rekam_medik
 * @property string $pasien_jenisidentitas
 * @property string $pasien_noidentitas
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property integer $rt
 * @property integer $rw
 * @property integer $kelurahan_id
 * @property string $kelurahan_nama
 * @property integer $kecamatan_id
 * @property string $kecamatan_nama
 * @property integer $kabupaten_id
 * @property string $kabupaten_nama
 * @property integer $propinsi_id
 * @property string $propinsi_nama
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property integer $anakke
 * @property integer $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $alamatemail
 * @property string $nama_ibu
 * @property string $nama_ayah
 * @property integer $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property integer $pasienadmisi_id
 * @property string $tgl_admisi
 * @property integer $pegawai_id
 * @property string $nomorindukpegawai
 * @property string $pegawai_jenisidentitas
 * @property string $pegawai_noidentitas
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property integer $penjualanresep_id
 * @property string $tglresep
 * @property string $noresep
 * @property string $tglpenjualan
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_loginpemakai_id
 * @property integer $update_loginpemakai_id
 * @property integer $create_ruangan
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property integer $penjamin_id
 * @property string $penjamin_nama
 * @property integer $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $umur
 * @property string $nomorindukpasien
 * @property boolean $is_deleted
 * @property integer $lookup_id
 * @property string $jenispenjualan
 */
class InformasiResepturView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inforeseptur_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_id', 'reseptur_id', 'instalasireseptur_id', 'ruanganreseptur_id', 'pasien_id', 'rt', 'rw', 'kelurahan_id', 'kecamatan_id', 'kabupaten_id', 'propinsi_id', 'anakke', 'jumlah_bersaudara', 'pendaftaran_id', 'pasienadmisi_id', 'pegawai_id', 'penjualanresep_id', 'create_loginpemakai_id', 'update_loginpemakai_id', 'create_ruangan', 'carabayar_id', 'penjamin_id', 'jeniskasuspenyakit_id', 'lookup_id'], 'integer'],
            [['tglreseptur', 'tanggal_lahir', 'tgl_pendaftaran', 'tgl_admisi', 'tglresep', 'tglpenjualan', 'create_time', 'update_time'], 'safe'],
            [['alamat_pasien', 'noresep'], 'string'],
            [['is_deleted'], 'boolean'],
            [['instalasi_nama', 'ruangan_nama', 'noreseptur', 'instalasireseptur_nama', 'ruanganreseptur_nama', 'nama_pasien', 'kelurahan_nama', 'kecamatan_nama', 'kabupaten_nama', 'propinsi_nama', 'nama_ibu', 'nama_ayah', 'nama_pegawai', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['fileresep'], 'string', 'max' => 500],
            [['no_rekam_medik', 'gelardepan'], 'string', 'max' => 10],
            [['pasien_jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien', 'no_pendaftaran', 'pegawai_jenisidentitas'], 'string', 'max' => 20],
            [['pasien_noidentitas', 'nama_bin', 'nomorindukpegawai', 'umur', 'nomorindukpasien'], 'string', 'max' => 30],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['alamatemail', 'pegawai_noidentitas', 'jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jenispenjualan'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'reseptur_id' => 'Reseptur ID',
            'tglreseptur' => 'Tglreseptur',
            'noreseptur' => 'Noreseptur',
            'instalasireseptur_id' => 'Instalasireseptur ID',
            'instalasireseptur_nama' => 'Instalasireseptur Nama',
            'ruanganreseptur_id' => 'Ruanganreseptur ID',
            'ruanganreseptur_nama' => 'Ruanganreseptur Nama',
            'fileresep' => 'Fileresep',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'pasien_jenisidentitas' => 'Pasien Jenisidentitas',
            'pasien_noidentitas' => 'Pasien Noidentitas',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'kelurahan_id' => 'Kelurahan ID',
            'kelurahan_nama' => 'Kelurahan Nama',
            'kecamatan_id' => 'Kecamatan ID',
            'kecamatan_nama' => 'Kecamatan Nama',
            'kabupaten_id' => 'Kabupaten ID',
            'kabupaten_nama' => 'Kabupaten Nama',
            'propinsi_id' => 'Propinsi ID',
            'propinsi_nama' => 'Propinsi Nama',
            'statusperkawinan' => 'Statusperkawinan',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'warga_negara' => 'Warga Negara',
            'alamatemail' => 'Alamatemail',
            'nama_ibu' => 'Nama Ibu',
            'nama_ayah' => 'Nama Ayah',
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_admisi' => 'Tgl Admisi',
            'pegawai_id' => 'Pegawai ID',
            'nomorindukpegawai' => 'Nomorindukpegawai',
            'pegawai_jenisidentitas' => 'Pegawai Jenisidentitas',
            'pegawai_noidentitas' => 'Pegawai Noidentitas',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'penjualanresep_id' => 'Penjualanresep ID',
            'tglresep' => 'Tglresep',
            'noresep' => 'Noresep',
            'tglpenjualan' => 'Tglpenjualan',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'create_loginpemakai_id' => 'Create Loginpemakai ID',
            'update_loginpemakai_id' => 'Update Loginpemakai ID',
            'create_ruangan' => 'Create Ruangan',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'umur' => 'Umur',
            'nomorindukpasien' => 'Nomorindukpasien',
            'is_deleted' => 'Is Deleted',
            'lookup_id' => 'Lookup ID',
            'jenispenjualan' => 'Jenispenjualan',
        ];
    }
}
