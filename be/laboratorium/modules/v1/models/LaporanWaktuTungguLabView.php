<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanwaktutunggulab_v".
 *
 * @property string $tipe
 * @property int $pasienmasukpenunjang_id
 * @property int $tindakanpelayanan_id
 * @property int $daftartindakan_id
 * @property int $pemeriksaanlab_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $dokter_id
 * @property string $dokter
 * @property int $samplelab_id
 * @property string $nama_sample
 * @property string $pemeriksaanlab_nama
 * @property string $tglmasukpenunjang
 * @property string $tgl_ambilsample
 * @property string $jam_ambilsample
 * @property string $tgl_hasilpemeriksaanlab
 * @property string $tgl_expertise
 */
class LaporanWaktuTungguLabView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanwaktutunggulab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe'], 'string'],
            [['pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'pemeriksaanlab_id', 'dokter_id', 'samplelab_id'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'pemeriksaanlab_id', 'dokter_id', 'samplelab_id'], 'integer'],
            [['tglmasukpenunjang', 'tgl_ambilsample', 'jam_ambilsample', 'tgl_hasilpemeriksaanlab', 'tgl_expertise'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter'], 'string', 'max' => 50],
            [['nama_sample'], 'string', 'max' => 100],
            [['pemeriksaanlab_nama'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe' => 'Tipe',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'dokter_id' => 'Dokter ID',
            'dokter' => 'Dokter',
            'samplelab_id' => 'Samplelab ID',
            'nama_sample' => 'Nama Sample',
            'pemeriksaanlab_nama' => 'Pemeriksaanlab Nama',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'tgl_ambilsample' => 'Tgl Ambilsample',
            'jam_ambilsample' => 'Jam Ambilsample',
            'tgl_hasilpemeriksaanlab' => 'Tgl Hasilpemeriksaanlab',
            'tgl_expertise' => 'Tgl Expertise',
        ];
    }
}
