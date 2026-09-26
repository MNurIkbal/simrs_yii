<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpemeriksaanlab_v".
 *
 * @property string $tipe
 * @property int $pasienmasukpenunjang_id
 * @property string $tglmasukpenunjang
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tanggal_lahir
 * @property int $pegawai_id
 * @property string $dokter
 * @property int $kelompokpemeriksaanlab_id
 * @property string $nama_kelompok
 * @property int $jenispemeriksaanlab_id
 * @property string $jenispemeriksaanlab_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property double $tarif_satuan
 * @property int $qty_tindakan
 * @property double $tarifcyto_tindakan
 * @property double $tarif_tindakan
 * @property int $ruangan_id
 * @property string $ruangan_nama
 */
class LaporanPemeriksaanLabView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpemeriksaanlab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe', 'tipepaket_nama'], 'string'],
            [['pasienmasukpenunjang_id', 'pendaftaran_id', 'pasien_id', 'pegawai_id', 'kelompokpemeriksaanlab_id', 'jenispemeriksaanlab_id', 'daftartindakan_id', 'tipepaket_id', 'qty_tindakan'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'pendaftaran_id', 'pasien_id', 'pegawai_id', 'kelompokpemeriksaanlab_id', 'jenispemeriksaanlab_id', 'daftartindakan_id', 'tipepaket_id', 'qty_tindakan'], 'integer'],
            [['tglmasukpenunjang', 'tanggal_lahir'], 'safe'],
            [['tarif_satuan', 'tarifcyto_tindakan', 'tarif_tindakan'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['dokter'], 'string', 'max' => 50],
            [['nama_kelompok'], 'string', 'max' => 255],
            [['jenispemeriksaanlab_nama'], 'string', 'max' => 30],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['ruangan_id'], 'integer'],
            [['ruangan_nama'], 'string'],
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
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tanggal_lahir' => 'Tanggal Lahir',
            'pegawai_id' => 'Pegawai ID',
            'dokter' => 'Dokter',
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
            'nama_kelompok' => 'Nama Kelompok',
            'jenispemeriksaanlab_id' => 'Jenispemeriksaanlab ID',
            'jenispemeriksaanlab_nama' => 'Jenispemeriksaanlab Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'tarif_satuan' => 'Tarif Satuan',
            'qty_tindakan' => 'Qty Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'tarif_tindakan' => 'Tarif Tindakan',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
        ];
    }
}
