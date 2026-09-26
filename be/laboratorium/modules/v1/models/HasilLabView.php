<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasillab_v".
 *
 * @property int $hasilpemeriksaanlab_id
 * @property string $nohasilperiksalab
 * @property string $tgl_hasilpemeriksaanlab
 * @property string $penganggung_jawab
 * @property string $no_pendaftaran
 * @property string $no_masukpenunjang
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $alamat_pasien
 * @property string $umur
 * @property string $jenis_kelamin
 * @property string $nama_pegawai
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $expertise
 * @property string $nama_sample
 */
class HasilLabView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasillab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hasilpemeriksaanlab_id'], 'default', 'value' => null],
            [['hasilpemeriksaanlab_id'], 'integer'],
            [['tgl_hasilpemeriksaanlab'], 'safe'],
            [['alamat_pasien', 'expertise'], 'string'],
            [['nohasilperiksalab', 'no_pendaftaran', 'no_masukpenunjang'], 'string', 'max' => 20],
            [['penganggung_jawab', 'nama_pasien', 'nama_pegawai', 'ruangan_nama'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['umur'], 'string', 'max' => 30],
            [['jenis_kelamin'], 'string', 'max' => 200],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur'], 'string', 'max' => 255],
            [['nama_sample'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanlab_id' => 'Hasilpemeriksaanlab ID',
            'nohasilperiksalab' => 'Nohasilperiksalab',
            'tgl_hasilpemeriksaanlab' => 'Tgl Hasilpemeriksaanlab',
            'penganggung_jawab' => 'Penganggung Jawab',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'alamat_pasien' => 'Alamat Pasien',
            'umur' => 'Umur',
            'jenis_kelamin' => 'Jenis Kelamin',
            'nama_pegawai' => 'Nama Pegawai',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'expertise' => 'Expertise',
            'nama_sample' => 'Nama Sample',
        ];
    }
}
