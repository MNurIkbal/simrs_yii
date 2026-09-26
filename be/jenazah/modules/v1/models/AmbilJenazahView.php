<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ambiljenazah_v".
 *
 * @property int $ambiljenazah_id
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $nama_pengambil
 * @property string $pekerjaan
 * @property string $alamat
 * @property string $no_identitas
 * @property string $tempat_lahir
 * @property string $tgl_lahir
 * @property string $tgl_ambil
 * @property string $ruangan_nama
 * @property string $hubungan_keluarga
 * @property string $nama_pasien
 * @property string $tempat_lahirpasien
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property string $kondisi
 * @property string $tgl_meninggal
 * @property int $ruanganasal_id
 * @property string $ruangan_asal
 * @property string $nama_pegawai
 */
class AmbilJenazahView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ambiljenazah_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambiljenazah_id', 'pendaftaran_id', 'ruanganasal_id'], 'default', 'value' => null],
            [['ambiljenazah_id', 'pendaftaran_id', 'ruanganasal_id'], 'integer'],
            [['alamat', 'alamat_pasien', 'kondisi'], 'string'],
            [['tgl_lahir', 'tgl_ambil', 'tanggal_lahir', 'tgl_meninggal'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pengambil', 'tempat_lahir'], 'string', 'max' => 100],
            [['pekerjaan', 'no_identitas'], 'string', 'max' => 255],
            [['ruangan_nama', 'nama_pasien', 'ruangan_asal', 'nama_pegawai'], 'string', 'max' => 50],
            [['hubungan_keluarga'], 'string', 'max' => 200],
            [['tempat_lahirpasien'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambiljenazah_id' => 'Ambiljenazah ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pengambil' => 'Nama Pengambil',
            'pekerjaan' => 'Pekerjaan',
            'alamat' => 'Alamat',
            'no_identitas' => 'No Identitas',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tgl Lahir',
            'tgl_ambil' => 'Tgl Ambil',
            'ruangan_nama' => 'Ruangan Nama',
            'hubungan_keluarga' => 'Hubungan Keluarga',
            'nama_pasien' => 'Nama Pasien',
            'tempat_lahirpasien' => 'Tempat Lahirpasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'kondisi' => 'Kondisi',
            'tgl_meninggal' => 'Tgl Meninggal',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangan_asal' => 'Ruangan Asal',
            'nama_pegawai' => 'Nama Pegawai',
        ];
    }
}
