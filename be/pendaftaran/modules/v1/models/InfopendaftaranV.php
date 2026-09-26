<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopendaftaran_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $pasien_id
 * @property string $tgl_masuk
 * @property string $tgl_keluar
 * @property string $umur
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $nosep
 * @property string $nokartuasuransi
 * @property string $jeniskelamin_id
 * @property string $jeniskelamin_nama
 * @property string $tanggal_lahir
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $dokter_id
 * @property string $dokter_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $jnspelayanan
 * @property string $jenisidentitas_id
 * @property string $jenisidentitas_nama
 * @property string $no_identitas_pasien
 * @property string $additional_pasien
 */
class InfopendaftaranV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopendaftaran_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'instalasi_id', 'pasien_id', 'carakeluar_id', 'dokter_id', 'kelaspelayanan_id', 'jnspelayanan'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'instalasi_id', 'pasien_id', 'carakeluar_id', 'dokter_id', 'kelaspelayanan_id', 'jnspelayanan'], 'integer'],
            [['tgl_masuk', 'tgl_keluar', 'tanggal_lahir'], 'safe'],
            [['jeniskelamin_nama', 'jenisidentitas_nama', 'additional_pasien'], 'string'],
            [['instalasi_nama', 'nokartuasuransi', 'nama_pasien', 'dokter_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['umur', 'no_identitas_pasien'], 'string', 'max' => 30],
            [['carakeluar_nama', 'nosep', 'no_rekam_medik'], 'string', 'max' => 100],
            [['jeniskelamin_id', 'jenisidentitas_id'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'pasien_id' => 'Pasien ID',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'umur' => 'Umur',
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'nosep' => 'Nosep',
            'nokartuasuransi' => 'Nokartuasuransi',
            'jeniskelamin_id' => 'Jeniskelamin ID',
            'jeniskelamin_nama' => 'Jeniskelamin Nama',
            'tanggal_lahir' => 'Tanggal Lahir',
            'nama_pasien' => 'Nama Pasien',
            'no_rekam_medik' => 'No Rekam Medik',
            'dokter_id' => 'Dokter ID',
            'dokter_nama' => 'Dokter Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'jnspelayanan' => 'Jnspelayanan',
            'jenisidentitas_id' => 'Jenisidentitas ID',
            'jenisidentitas_nama' => 'Jenisidentitas Nama',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'additional_pasien' => 'Additional Pasien',
        ];
    }
}
