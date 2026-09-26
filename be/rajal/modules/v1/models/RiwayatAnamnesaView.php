<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-13 17:20:28
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "riwayatasesmenmedis_v".
 *
 * @property int $anamesa_id
 * @property int $pendaftaran_id
 * @property string $ruangan_nama
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $jenis_kelamin
 * @property date $tanggal_lahir
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property datetime $tgl_anamnesis
 * @property string $dokter
 * @property string $perawat
 * @property text $keluhan_utama
 * @property text $keluhan_tambahan
 * @property text $riwayat_perjalananpasien
 * @property string $lama_sakit
 * @property string $riwayat_penyakitterdahulu
 * @property string $riwayat_penyakitkeluarga
 * @property string $riwayat_imunisasi
 * @property bool $status_merokok
 * @property string $jmlrokok_btgperhari
 * @property string $pengobatan_ygsudahdilakukan
 * @property string $riwayat_makanan
 * @property string $riwayat_kelahiran
 * @property string $riwayat_alergiobat
 * @property text $keterangan_anamesa
 */
class RiwayatAnamnesaView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'riwayatanamnesa_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['anamesa_id', 'pendaftaran_id', 'ruangan_nama', 'no_pendaftaran', 'no_rekam_medik', 'nama_pasien', 'jenis_kelamin', 'tanggal_lahir', 'carabayar_nama', 'penjamin_nama', 'tgl_anamnesis', 'dokter', 'perawat', 'keluhan_utama','keluhan_tambahan', 'riwayat_perjalananpasien', 'lama_sakit', 'riwayat_penyakitterdahulu', 'riwayat_penyakitkeluarga', 'riwayat_imunisasi', 'status_merokok', 'jmlrokok_btgperhari', 'pengobatan_ygsudahdilakukan', 'riwayat_makanan', 'riwayat_kelahiran', 'riwayat_alergiobat', 'keterangan_anamesa'], 'default', 'value' => null],
            [['keterangan_anamesa', 'pendaftaran_id', 'jmlrokok_btgperhari'], 'integer'],
            [['ruangan_nama', 'no_pendaftaran', 'no_rekam_medik', 'nama_pasien', 'jenis_kelamin', 'carabayar_nama', 'penjamin_nama', 'dokter', 'perawat', 'lama_sakit', 'riwayat_penyakitterdahulu','riwayat_penyakitkeluarga', 'riwayat_imunisasi', 'pengobatan_ygsudahdilakukan', 'riwayat_makanan', 'riwayat_kelahiran', 'riwayat_alergiobat', 'keterangan_anamesa'], 'string'],
            [['tanggal_lahir'], 'safe'],
            [['status_merokok'], 'boolean'],
            [['keluhan_utama', 'keluhan_tambahan', 'riwayat_perjalananpasien', 'keterangan_anamesa'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'anamesa_id' => 'Anamesa ID',
            'pendaftaran_id' => 'Pendaftaran ID',

            'ruangan_nama' => 'Nama Ruangan',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No. Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tanggal_lahir' => 'Tanggal Lahir',
            'carabayar_nama' => 'Nama Cara Bayar',
            'penjamin_nama' => 'Nama Penjamin',
            'tgl_anamnesis' => 'Tanggal Anamnesis',
            'dokter' => 'Dokter',
            'perawat' => 'Perawat',
            'keluhan_utama' => 'Keluhan Utama',
            'keluhan_tambahan' => 'Keluhan Tambahan',
            'riwayat_perjalananpasien' => 'Riwayat Perjalanan Pasien',
            'lama_sakit' => 'Lama Sakit',
            'riwayat_penyakitterdahulu' => 'Riwayat Penyakit Terdahulu',
            'riwayat_penyakitkeluarga' => 'Riwayat Penyakit Keluarga',
            'riwayat_imunisasi' => 'Riwayat Imunisasi',
            'status_merokok' => 'Status Merokok',
            'jmlrokok_btgperhari' => 'Jumlah Rokok Batang Perhari',
            'pengobatan_ygsudahdilakukan' => 'Pengobatan Yang Sudah Dilakukan',
            'riwayat_makanan' => 'Riwayat Makanan',
            'riwayat_kelahiran' => 'Riwayat Kelahiran',
            'riwayat_alergiobat' => 'Riwayat Alergi Obat',
            'keterangan_anamesa' => 'Keterangan Anamesa',
        ];
    }
}
