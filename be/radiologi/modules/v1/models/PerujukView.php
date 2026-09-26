<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "perujuk_v".
 *
 * @property int $perujuk_id
 * @property int $asalrujukan_id
 * @property string $asalrujukan_nama
 * @property string $namaperujuk
 * @property string $spesialis
 * @property string $alamatlengkap
 * @property string $notelp
 */
class PerujukView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'perujuk_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perujuk_id', 'asalrujukan_id'], 'default', 'value' => null],
            [['perujuk_id', 'asalrujukan_id'], 'integer'],
            [['alamatlengkap'], 'string'],
            [['asalrujukan_nama', 'spesialis'], 'string', 'max' => 50],
            [['namaperujuk', 'notelp'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'perujuk_id' => 'Perujuk ID',
            'asalrujukan_id' => 'Asalrujukan ID',
            'asalrujukan_nama' => 'Asalrujukan Nama',
            'namaperujuk' => 'Namaperujuk',
            'spesialis' => 'Spesialis',
            'alamatlengkap' => 'Alamatlengkap',
            'notelp' => 'Notelp',
        ];
    }
}
