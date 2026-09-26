<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "laporanpersalinan_v".
 *
 * @property int $pendaftaranibu_id
 * @property string $no_pendaftaran_ibu
 * @property string $nama_ibu
 * @property string $no_rekam_medik_ibu
 * @property int $pendaftaranbayi_id
 * @property string $nama_bayi
 * @property string $no_rekam_medik_bayi
 * @property string $tgl_lahir_bayi
 * @property double $berat_badan
 * @property int $jenis_persalinan_id
 * @property string $jenis_persalinan_nama
 */
class LaporanpersalinanV extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpersalinan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaranibu_id', 'pendaftaranbayi_id', 'jenis_persalinan_id'], 'default', 'value' => null],
            [['pendaftaranibu_id', 'pendaftaranbayi_id', 'jenis_persalinan_id'], 'integer'],
            [['tgl_lahir_bayi'], 'safe'],
            [['berat_badan'], 'number'],
            [['jenis_persalinan_nama'], 'string'],
            [['no_pendaftaran_ibu'], 'string', 'max' => 20],
            [['nama_ibu', 'nama_bayi'], 'string', 'max' => 50],
            [['no_rekam_medik_ibu', 'no_rekam_medik_bayi'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaranibu_id' => 'Pendaftaranibu ID',
            'no_pendaftaran_ibu' => 'No Pendaftaran Ibu',
            'nama_ibu' => 'Nama Ibu',
            'no_rekam_medik_ibu' => 'No Rekam Medik Ibu',
            'pendaftaranbayi_id' => 'Pendaftaranbayi ID',
            'nama_bayi' => 'Nama Bayi',
            'no_rekam_medik_bayi' => 'No Rekam Medik Bayi',
            'tgl_lahir_bayi' => 'Tgl Lahir Bayi',
            'berat_badan' => 'Berat Badan',
            'jenis_persalinan_id' => 'Jenis Persalinan ID',
            'jenis_persalinan_nama' => 'Jenis Persalinan Nama',
        ];
    }
}
