<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporansensusharianri_pasienpindahan_v".
 *
 * @property string $tgl_admisi
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $ruangan_skrg
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $diagnosa_nama
 * @property string $tgl_masukkamar
 * @property int $lama_rawat
 * @property int $ruangan_id
 * @property string $ruangan_dari
 * @property int $instalasi_id
 * @property string $instalasi_dari
 * @property string $kamar_dari
 * @property string $tempattidur_dari
 * @property string $dokter_admisi
 * @property string $tgl_pasienplg
 * @property string $tgl_pindahkamar
 */
class LaporansensusharianriPasienpindahanV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporansensusharianri_pasienpindahan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_admisi', 'tgl_masukkamar', 'tgl_pasienplg', 'tgl_pindahkamar'], 'safe'],
            [['pasien_id', 'kelaspelayanan_id', 'penjamin_id', 'lama_rawat', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['pasien_id', 'kelaspelayanan_id', 'penjamin_id', 'lama_rawat', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['nama_pasien', 'kelaspelayanan_nama', 'ruangan_skrg', 'ruangan_dari', 'instalasi_dari', 'dokter_admisi'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 100],
            [['penjamin_nama', 'diagnosa_nama'], 'string', 'max' => 200],
            [['kamar_dari'], 'string', 'max' => 25],
            [['tempattidur_dari'], 'string', 'max' => 255],
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
            'ruangan_skrg' => 'Ruangan Skrg',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'diagnosa_nama' => 'Diagnosa Nama',
            'tgl_masukkamar' => 'Tgl Masukkamar',
            'lama_rawat' => 'Lama Rawat',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_dari' => 'Ruangan Dari',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_dari' => 'Instalasi Dari',
            'kamar_dari' => 'Kamar Dari',
            'tempattidur_dari' => 'Tempattidur Dari',
            'dokter_admisi' => 'Dokter Admisi',
            'tgl_pasienplg' => 'Tgl Pasienplg',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
        ];
    }
}
