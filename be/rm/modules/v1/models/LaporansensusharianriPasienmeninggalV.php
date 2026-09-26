<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporansensusharianri_pasienmeninggal_v".
 *
 * @property string $tgl_admisi
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $diagnosa_nama
 * @property string $tgl_masukkamar
 * @property int $lama_rawat_kur48
 * @property int $lama_rawat_leb48
 * @property int $ruangan_id
 * @property string $ruangan_ke
 * @property int $instalasi_id
 * @property string $instalasi_ke
 * @property string $kamar_ke
 * @property string $tempattidur_ke
 * @property string $dokter_admisi
 * @property string $kondisi_keluar_id
 * @property string $kondisi_keluar_nama
 */
class LaporansensusharianriPasienmeninggalV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporansensusharianri_pasienmeninggal_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_admisi', 'tgl_masukkamar'], 'safe'],
            [['pasien_id', 'kelaspelayanan_id', 'penjamin_id', 'lama_rawat_kur48', 'lama_rawat_leb48', 'ruangan_id', 'instalasi_id', 'kondisi_keluar_id', 'kondisi_keluar_nama'], 'default', 'value' => null],
            [['pasien_id', 'kelaspelayanan_id', 'penjamin_id', 'lama_rawat_kur48', 'lama_rawat_leb48', 'ruangan_id', 'instalasi_id', 'kondisi_keluar_id'], 'integer'],
            [['nama_pasien', 'kelaspelayanan_nama', 'ruangan_ke', 'instalasi_ke', 'dokter_admisi'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 100],
            [['penjamin_nama', 'diagnosa_nama'], 'string', 'max' => 200],
            [['kamar_ke'], 'string', 'max' => 25],
            [['tempattidur_ke', 'kondisi_keluar_nama'], 'string', 'max' => 255],
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
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'diagnosa_nama' => 'Diagnosa Nama',
            'tgl_masukkamar' => 'Tgl Masukkamar',
            'lama_rawat_kur48' => 'Lama Rawat Kur48',
            'lama_rawat_leb48' => 'Lama Rawat Leb48',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_ke' => 'Ruangan Ke',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_ke' => 'Instalasi Ke',
            'kamar_ke' => 'Kamar Ke',
            'tempattidur_ke' => 'Tempattidur Ke',
            'dokter_admisi' => 'Dokter Admisi',
            'kondisi_keluar_id' => 'Kondisi Keluar ID',
            'kondisi_keluar_nama' => 'Kondisi Keluar Nama',
        ];
    }
}
