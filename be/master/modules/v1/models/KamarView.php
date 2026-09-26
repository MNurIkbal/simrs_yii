<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kamar_v".
 *
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $kamarruangan_nokamar
 * @property int $kamarruangan_jenis
 * @property string $jenis_kamar
 */
class KamarView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kamar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kamarruangan_id', 'ruangan_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'kamarruangan_jenis'], 'default', 'value' => null],
            [['kamarruangan_id', 'ruangan_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'kamarruangan_jenis'], 'integer'],
            [['ruangan_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['jenis_kamar'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'kamarruangan_jenis' => 'Kamarruangan Jenis',
            'jenis_kamar' => 'Jenis Kamar',
        ];
    }
}
