<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "nilaipemeriksaanlabdetail_v".
 *
 * @property int $pemeriksaanlab_id
 * @property string $daftartindakan_nama
 * @property int $nilairujukan_id
 * @property int $jenis_kelamin
 * @property string $jenis_kelamin_nama
 * @property int $golonganumur_id
 * @property string $gol_umurlab_nama
 * @property string $gol_umurlab_minimal
 * @property string $gol_umurlab_maksimal
 * @property string $nilai_rujukan
 * @property string $nilai_min
 * @property string $nilai_max
 * @property int $satuan_hasillab
 * @property string $satuanlab_nama
 * @property string $keterangan
 */
class NilaiPemeriksaanLabDetailView extends \Doco\components\DocoActiveRecord
{
    public $satuan_hasillab;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nilaipemeriksaanlabdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemeriksaanlab_id', 'nilairujukan_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab'], 'default', 'value' => null],
            [['pemeriksaanlab_id', 'nilairujukan_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab'], 'integer'],
            [['gol_umurlab_minimal', 'gol_umurlab_maksimal'], 'number'],
            [['daftartindakan_nama', 'jenis_kelamin_nama'], 'string', 'max' => 200],
            [['gol_umurlab_nama'], 'string', 'max' => 25],
            [['nilai_rujukan', 'nilai_min', 'nilai_max', 'satuanlab_nama', 'keterangan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'nilairujukan_id' => 'Nilairujukan ID',
            'jenis_kelamin' => 'Jenis Kelamin',
            'jenis_kelamin_nama' => 'Jenis Kelamin Nama',
            'golonganumur_id' => 'Golonganumur ID',
            'gol_umurlab_nama' => 'Gol Umurlab Nama',
            'gol_umurlab_minimal' => 'Gol Umurlab Minimal',
            'gol_umurlab_maksimal' => 'Gol Umurlab Maksimal',
            'nilai_rujukan' => 'Nilai Rujukan',
            'nilai_min' => 'Nilai Min',
            'nilai_max' => 'Nilai Max',
            'satuan_hasillab' => 'Satuan Hasillab',
            'satuanlab_nama' => 'Satuanlab Nama',
            'keterangan' => 'Keterangan',
        ];
    }
}
