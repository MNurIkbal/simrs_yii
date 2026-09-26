<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi4_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $penggunaancairan_id
 * @property string $kegiatan
 * @property string $cairan_masuk
 * @property string $cairan_keluar
 * @property string $keterangan
 */
class PenggunaanCairanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi4_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'penggunaancairan_id'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'penggunaancairan_id'], 'integer'],
            [['keterangan'], 'string'],
            [['kegiatan', 'cairan_masuk', 'cairan_keluar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'penggunaancairan_id' => 'Penggunaancairan ID',
            'kegiatan' => 'Kegiatan',
            'cairan_masuk' => 'Cairan Masuk',
            'cairan_keluar' => 'Cairan Keluar',
            'keterangan' => 'Keterangan',
        ];
    }
}
