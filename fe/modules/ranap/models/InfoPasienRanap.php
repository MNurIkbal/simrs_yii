<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "infopasienri_v".
 *
 * @property int $pasienadmisi_id
 * @property int $pendaftaran_id
 * @property int $dokter_pendaftaran_id
 * @property int $dokter_admisi_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $klsrawat
 * @property int $kelaspelayanan_id
 * @property int $ruangan_id
 * @property string $tgl_admisi
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $jenis_kelamin
 * @property string $dokter_pendaftaran
 * @property string $dokter_admisi
 * @property int $hak_kelas
 * @property string $kelas_pelayanan
 * @property string $jeniskasuspenyakit_nama
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $tgl_pindahkamar
 * @property string $tgl_pulang
 * @property string $tanggal_lahir
 * @property string $umur
 * @property string $rencana_pulang
 * @property string $sumber_info
 * @property string $sumber_hubungan
 * @property string $r_alergiobat
 * @property bool $is_hamil
 * @property string $jeniskasuspenyakit_id
 */
class InfoPasienRanap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienri_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'tgl_pindahkamar', 'ruangan_nama'], 'required'],
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'hak_kelas'], 'default', 'value' => null],
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'hak_kelas'], 'integer'],
            [['tgl_admisi', 'tgl_pindahkamar', 'tgl_pulang', 'tanggal_lahir', 'rencana_pulang','jeniskasuspenyakit_id'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['is_hamil'], 'boolean'],
            [['nama_pasien', 'dokter_pendaftaran', 'dokter_admisi', 'kelas_pelayanan', 'ruangan_nama'], 'string', 'max' => 50],
            [['jenis_kelamin', 'sumber_hubungan', 'sumber_info', 'carabayar_nama', 'penjamin_nama', 'umur', 'r_alergiobat'], 'string', 'max' => 200],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokter_pendaftaran_id' => 'Dokter Pendaftaran ID',
            'dokter_admisi_id' => 'Dokter Admisi ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'carabayar_nama' => 'Carabayar',
            'penjamin_nama' => 'Penjamin',
            'klsrawat' => 'Klsrawat',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_admisi' => 'Tgl Admisi',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'dokter_pendaftaran' => 'Dokter Pendaftaran',
            'dokter_admisi' => 'Dokter Admisi',
            'hak_kelas' => 'Hak Kelas',
            'kelas_pelayanan' => 'Kelas Pelayanan',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'ruangan_nama' => 'Ruangan',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'tgl_pindahkamar' => 'Tanggal Pindah',
            'tgl_pulang' => 'Tgl Pulang',
            'tanggal_lahir' => 'Tgl Lahir',
            'rencana_pulang' => 'Rencana Pulang',
            'r_alergiobat' => 'Alergi',
            'is_hamil' => 'Hamil / Menyusui',
            'sumber_info' => 'sumber informasi',
            'sumber_hubungan' => 'hubungan sumber',
            'umur' => 'Umur',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
        ];
    }
}
