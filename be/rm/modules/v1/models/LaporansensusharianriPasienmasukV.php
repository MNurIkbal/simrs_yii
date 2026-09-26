<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporansensusharianri_pasienmasuk_v".
 *
 * @property string $tgl_admisi
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property string $penjamin_nama
 * @property string $nama_dokter
 * @property string $diagnosa_nama
 */
class LaporansensusharianriPasienmasukV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporansensusharianri_pasienmasuk_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_admisi'], 'safe'],
            [['pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['nama_pasien', 'kelaspelayanan_nama', 'ruangan_nama', 'instalasi_nama', 'nama_dokter'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 100],
            [['penjamin_nama', 'diagnosa_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_admisi' => 'Tgl Admisi',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'no_rekam_medik' => 'No Rekam Medik',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'nama_dokter' => 'Nama Dokter',
            'diagnosa_nama' => 'Diagnosa Nama',
        ];
    }
}
