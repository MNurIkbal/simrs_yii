<?php

namespace app\modules\rm\models;

use Yii;

/**
 * This is the model class for table "laporanpemakaianbarang_v".
 *
 * @property int $barang_id
 * @property string $barang_nama
 * @property string $barang_satuan
 * @property string $tgl_pemakaianbarang
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $jumlah_pakai
 */
class LaporanPemakaianBarang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanpemakaianbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['barang_id', 'pegawai_id', 'jumlah_pakai'], 'default', 'value' => null],
            [['barang_id', 'pegawai_id', 'jumlah_pakai'], 'integer'],
            [['tgl_pemakaianbarang'], 'safe'],
            [['barang_nama'], 'string', 'max' => 100],
            [['barang_satuan', 'nama_pegawai'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'barang_satuan' => 'Barang Satuan',
            'tgl_pemakaianbarang' => 'Tgl Pemakaianbarang',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'jumlah_pakai' => 'Jumlah Pakai',
        ];
    }
}
