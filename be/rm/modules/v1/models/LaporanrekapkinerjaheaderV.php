<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanrekapkinerjaheader_v".
 *
 * @property int $kamarruangan_id
 * @property int $kelaspelayanan_id
 * @property int $urutankelas
 * @property string $kamarruangan_nokamar
 * @property string $kelaspelayanan_nama
 * @property int $kapasitas
 * @property int $tersedia
 */
class LaporanrekapkinerjaheaderV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanrekapkinerjaheader_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kamarruangan_id', 'kelaspelayanan_id', 'urutankelas', 'kapasitas', 'tersedia'], 'default', 'value' => null],
            [['kamarruangan_id', 'kelaspelayanan_id', 'urutankelas', 'kapasitas', 'tersedia'], 'integer'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['kelaspelayanan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamarruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'urutankelas' => 'Urutankelas',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'kapasitas' => 'Kapasitas',
            'tersedia' => 'Tersedia',
        ];
    }
}
