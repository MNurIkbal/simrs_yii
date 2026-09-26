<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi6_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pemeriksaanpelengkap_id
 * @property int $daftartindakan_id
 * @property string $pemeriksaan_pelengkap
 * @property string $nama_jaringan
 * @property int $ukuran
 */
class PemeriksaanPelengkapView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi6_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'pemeriksaanpelengkap_id', 'daftartindakan_id', 'ukuran'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'pemeriksaanpelengkap_id', 'daftartindakan_id', 'ukuran'], 'integer'],
            [['pemeriksaan_pelengkap'], 'string', 'max' => 200],
            [['nama_jaringan'], 'string', 'max' => 255],
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
            'pemeriksaanpelengkap_id' => 'Pemeriksaanpelengkap ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'pemeriksaan_pelengkap' => 'Pemeriksaan Pelengkap',
            'nama_jaringan' => 'Nama Jaringan',
            'ukuran' => 'Ukuran',
        ];
    }
}
