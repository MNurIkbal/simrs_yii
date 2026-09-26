<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforekomendasibarangdetail_v".
 *
 * @property int $rekomendasibarang_id
 * @property int $rekomendasibarangdetail_id
 * @property int $barang_id
 * @property string $barang_nama
 * @property int $nilai_ro
 * @property int $ro_stok
 * @property int $rekomendasi
 */
class InfoRekomendasiBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforekomendasibarangdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekomendasibarang_id', 'rekomendasibarangdetail_id', 'barang_id', 'nilai_ro', 'ro_stok', 'rekomendasi'], 'default', 'value' => null],
            [['rekomendasibarang_id', 'rekomendasibarangdetail_id', 'barang_id', 'nilai_ro', 'ro_stok', 'rekomendasi'], 'integer'],
            [['barang_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekomendasibarang_id' => 'Rekomendasibarang ID',
            'rekomendasibarangdetail_id' => 'Rekomendasibarangdetail ID',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'nilai_ro' => 'Nilai Ro',
            'ro_stok' => 'Ro Stok',
            'rekomendasi' => 'Rekomendasi',
        ];
    }
}
