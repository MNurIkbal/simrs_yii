<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoorderanlab_v".
 *
 * @property int $pasienkirimkeunitlain_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property string $tgl_rujukan
 * @property string $no_rujukan
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $umur
 * @property string $jenis_kelamin
 * @property string $kelaspelayanan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property int $pegawai_id
 * @property string $dokter_perujuk
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $status_penunjang
 * @property int $kelaspelayanan_id
 * @property int $jeniskasuspenyakit_id
 * @property int $ruangan_id
 */
class InfoOrderanLabView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoorderanlab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienkirimkeunitlain_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'instalasi_id', 'pegawai_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'ruangan_id'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'instalasi_id', 'pegawai_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'ruangan_id'], 'integer'],
            [['tgl_rujukan'], 'safe'],
            [['kamarruangan_nokamar', 'no_tempattidur'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rujukan', 'status_penunjang'], 'string', 'max' => 100],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'kelaspelayanan_nama', 'instalasi_nama', 'ruangan_nama', 'dokter_perujuk', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['umur'], 'string', 'max' => 30],
            [['jenis_kelamin'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_rujukan' => 'Tgl Rujukan',
            'no_rujukan' => 'No Rujukan',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'umur' => 'Umur',
            'jenis_kelamin' => 'Jenis Kelamin',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'pegawai_id' => 'Pegawai ID',
            'dokter_perujuk' => 'Dokter Perujuk',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'status_penunjang' => 'Status Penunjang',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'ruangan_id' => 'Ruangan ID',
        ];
    }
}
