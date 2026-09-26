<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "nilairujukan_v".
 *
 * @property int $nilairujukan_id
 * @property int $pemeriksaanlab_id
 * @property string $pemeriksaanlab_nama
 * @property int $kelompokpemeriksaanlab_id
 * @property string $nama_kelompok
 * @property string $nilairujukan_nama
 * @property int $jenis_kelamin
 * @property string $jenis_kelamin_nama
 * @property int $golonganumur_id
 * @property string $golonganumur_nama
 * @property double $nilai_min
 * @property double $nilai_max
 * @property int $satuan_hasillab
 * @property string $satuan_lab
 * @property string $text_value
 * @property string $positif_negatif
 * @property string $keterangan
 */
class NilaiRujukanViewForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nilairujukan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nilairujukan_id', 'pemeriksaanlab_id', 'kelompokpemeriksaanlab_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab'], 'default', 'value' => null],
            [['nilairujukan_id', 'pemeriksaanlab_id', 'kelompokpemeriksaanlab_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab'], 'integer'],
            [['nilai_min', 'nilai_max'], 'number'],
            [['pemeriksaanlab_nama'], 'string', 'max' => 500],
            [['nama_kelompok', 'nilairujukan_nama', 'text_value', 'keterangan'], 'string', 'max' => 255],
            [['jenis_kelamin_nama', 'satuan_lab', 'positif_negatif'], 'string', 'max' => 200],
            [['golonganumur_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nilairujukan_id' => 'Nilairujukan ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'pemeriksaanlab_nama' => 'Pemeriksaanlab Nama',
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
            'nama_kelompok' => 'Nama Kelompok',
            'nilairujukan_nama' => 'Nilairujukan Nama',
            'jenis_kelamin' => 'Jenis Kelamin',
            'jenis_kelamin_nama' => 'Jenis Kelamin Nama',
            'golonganumur_id' => 'Golonganumur ID',
            'golonganumur_nama' => 'Golonganumur Nama',
            'nilai_min' => 'Nilai Min',
            'nilai_max' => 'Nilai Max',
            'satuan_hasillab' => 'Satuan Hasillab',
            'satuan_lab' => 'Satuan Lab',
            'text_value' => 'Text Value',
            'positif_negatif' => 'Positif Negatif',
            'keterangan' => 'Keterangan',
        ];
    }
}
