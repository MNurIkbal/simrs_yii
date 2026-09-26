<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopindahkamar_v".
 *
 * @property int $pindahkamar_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pegawaipendaftaran_id
 * @property int $pegawaiadmisi_id
 * @property int $kelaspelayanan_id
 * @property int $ruangan_sekarang_id
 * @property int $ruangan_pindah_id
 * @property string $tgl_admisi
 * @property string $tgl_pindahkamar
 * @property string $no_rekam_medik
 * @property string $no_pendaftaran
 * @property string $nama_pasien
 * @property string $jenis_kelamin
 * @property string $dokter_admisi
 * @property string $dokter_pendaftaran
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $kelaspelayanan_nama
 * @property string $jeniskasuspenyakit_nama
 * @property string $ruangan_sekarang
 * @property string $kamar_sekarang
 * @property string $tempattidur_sekarang
 * @property string $ruangan_pindah
 * @property string $kamar_pindah
 * @property string $tempattidur_pindah
 */
class InfoPasienPindah extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopindahkamar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pindahkamar_id', 'pasien_id', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'jeniskasuspenyakit_id', 'pegawaipendaftaran_id', 'pegawaiadmisi_id', 'kelaspelayanan_id', 'ruangan_sekarang_id', 'ruangan_pindah_id'], 'default', 'value' => null],
            [['pindahkamar_id', 'pasien_id', 'pendaftaran_id', 'carabayar_id', 'penjamin_id', 'jeniskasuspenyakit_id', 'pegawaipendaftaran_id', 'pegawaiadmisi_id', 'kelaspelayanan_id', 'ruangan_sekarang_id', 'ruangan_pindah_id'], 'integer'],
            [['tgl_admisi', 'tgl_pindahkamar'], 'safe'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'dokter_admisi', 'dokter_pendaftaran', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama', 'ruangan_sekarang', 'ruangan_pindah'], 'string', 'max' => 50],
            [['jenis_kelamin'], 'string', 'max' => 200],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['kamar_sekarang', 'kamar_pindah'], 'string', 'max' => 25],
            [['tempattidur_sekarang', 'tempattidur_pindah'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pindahkamar_id' => 'Pindahkamar ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pegawaipendaftaran_id' => 'Pegawaipendaftaran ID',
            'pegawaiadmisi_id' => 'Pegawaiadmisi ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'ruangan_sekarang_id' => 'Ruangan Sekarang ID',
            'ruangan_pindah_id' => 'Ruangan Pindah ID',
            'tgl_admisi' => 'Tgl Admisi',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
            'no_rekam_medik' => 'No Rekam Medik',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'dokter_admisi' => 'Dokter Admisi',
            'dokter_pendaftaran' => 'Dokter Pendaftaran',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'ruangan_sekarang' => 'Ruangan Sekarang',
            'kamar_sekarang' => 'Kamar Sekarang',
            'tempattidur_sekarang' => 'Tempattidur Sekarang',
            'ruangan_pindah' => 'Ruangan Pindah',
            'kamar_pindah' => 'Kamar Pindah',
            'tempattidur_pindah' => 'Tempattidur Pindah',
        ];
    }
}
