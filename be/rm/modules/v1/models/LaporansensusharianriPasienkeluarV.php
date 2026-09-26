<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporansensusharianri_pasienkeluar_v".
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
 * @property string $diagnosa_nama
 * @property string $penjamin_nama
 * @property string $tgl_masukkamar
 * @property int $lama_rawat
 * @property string $nama_dokter
 */
class LaporansensusharianriPasienkeluarV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporansensusharianri_pasienkeluar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_admisi', 'tgl_masukkamar'], 'safe'],
            [['pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'instalasi_id', 'lama_rawat', 'jam_rawat'], 'default', 'value' => null],
            [['pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'instalasi_id', 'lama_rawat'], 'integer'],
            [['nama_pasien', 'kelaspelayanan_nama', 'ruangan_nama', 'instalasi_nama', 'nama_dokter'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 100],
            [['diagnosa_nama', 'penjamin_nama'], 'string', 'max' => 200],
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
            'diagnosa_nama' => 'Diagnosa Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'tgl_masukkamar' => 'Tgl Masukkamar',
            'lama_rawat' => 'Lama Rawat',
            'nama_dokter' => 'Nama Dokter',
        ];
    }
}
