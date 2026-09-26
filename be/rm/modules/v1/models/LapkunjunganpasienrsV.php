<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "lapkunjunganpasienrs_v".
 *
 * @property int $pendaftaran_id
 * @property string $jeniskelamin
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $jeniskasuspenyakit_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $dokterdpjp_id
 * @property string $tgl_pendaftaran
 * @property string $tgl_lahir
 * @property string $alamat_pasien
 * @property string $carabayar_penjamin
 * @property string $jeniskasuspenyakit_nama
 * @property string $instalasi_ruangan
 * @property string $dokterdpjp_nama
 * @property string $diagnosa_utama
 * @property string $diagnosa_penyerta
 * @property string $status_periksa
 */
class LapkunjunganpasienrsV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lapkunjunganpasienrs_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'carabayar_id', 'penjamin_id', 'jeniskasuspenyakit_id', 'instalasi_id', 'ruangan_id', 'dokterdpjp_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'carabayar_id', 'penjamin_id', 'jeniskasuspenyakit_id', 'instalasi_id', 'ruangan_id', 'dokterdpjp_id'], 'integer'],
            [['tgl_pendaftaran'], 'safe'],
            [['tgl_lahir', 'alamat_pasien', 'carabayar_penjamin', 'instalasi_ruangan', 'diagnosa_utama', 'diagnosa_penyerta', 'status_periksa'], 'string'],
            [['jeniskelamin'], 'string', 'max' => 20],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['dokterdpjp_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'jeniskelamin' => 'Jeniskelamin',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'dokterdpjp_id' => 'Dokterdpjp ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tgl_lahir' => 'Tgl Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'carabayar_penjamin' => 'Carabayar Penjamin',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'instalasi_ruangan' => 'Instalasi Ruangan',
            'dokterdpjp_nama' => 'Dokterdpjp Nama',
            'diagnosa_utama' => 'Diagnosa Utama',
            'diagnosa_penyerta' => 'Diagnosa Penyerta',
            'status_periksa' => 'Status Periksa',
        ];
    }
}