<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanwaktutunggurad_v".
 *
 * @property string $tipe
 * @property int $pasienmasukpenunjang_id
 * @property int $tindakanpelayanan_id
 * @property int $daftartindakan_id
 * @property int $pemeriksaanradiologi_id
 * @property string $tgl_kirimpasien
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $dokter_id
 * @property string $dokter
 * @property int $jenispemeriksaanrad_id
 * @property string $jenispemeriksaanrad_nama
 * @property string $pemeriksaanrad_nama
 * @property string $tglmasukpenunjang
 * @property string $tgl_ambilfoto
 * @property string $tgl_hasilrad
 */
class LaporanWaktuTungguRadView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanwaktutunggurad_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe'], 'string'],
            [['pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'pemeriksaanradiologi_id', 'dokter_id', 'jenispemeriksaanrad_id'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'pemeriksaanradiologi_id', 'dokter_id', 'jenispemeriksaanrad_id'], 'integer'],
            [['tgl_kirimpasien', 'tglmasukpenunjang', 'tgl_ambilfoto', 'tgl_hasilrad'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter'], 'string', 'max' => 50],
            [['jenispemeriksaanrad_nama', 'pemeriksaanrad_nama'], 'string', 'max' => 100],
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
            'pemeriksaanradiologi_id' => 'Pemeriksaanradiologi ID',
            'tgl_kirimpasien' => 'Tgl Kirimpasien',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'dokter_id' => 'Dokter ID',
            'dokter' => 'Dokter',
            'jenispemeriksaanrad_id' => 'Jenispemeriksaanrad ID',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
            'pemeriksaanrad_nama' => 'Pemeriksaanrad Nama',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'tgl_ambilfoto' => 'Tgl Ambilfoto',
            'tgl_hasilrad' => 'Tgl Hasilrad',
        ];
    }
}
