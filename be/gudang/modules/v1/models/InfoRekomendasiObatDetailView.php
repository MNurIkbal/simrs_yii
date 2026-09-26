<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforekomendasiobatdetail_v".
 *
 * @property int $rekomendasiobat_id
 * @property int $rekomendasiobatdetail_id
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $nilai_ro
 * @property int $ro_stok
 * @property int $rekomendasi
 */
class InfoRekomendasiObatDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforekomendasiobatdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekomendasiobat_id', 'rekomendasiobatdetail_id', 'obatalkes_id', 'nilai_ro', 'ro_stok', 'rekomendasi'], 'default', 'value' => null],
            [['rekomendasiobat_id', 'rekomendasiobatdetail_id', 'obatalkes_id', 'nilai_ro', 'ro_stok', 'rekomendasi'], 'integer'],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekomendasiobat_id' => 'Rekomendasiobat ID',
            'rekomendasiobatdetail_id' => 'Rekomendasiobatdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'nilai_ro' => 'Nilai Ro',
            'ro_stok' => 'Ro Stok',
            'rekomendasi' => 'Rekomendasi',
        ];
    }
}
