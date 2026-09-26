<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl1_2_indikatorrs".
 *
 * @property string $tahun
 * @property string $bor
 * @property string $avlos
 * @property string $bto
 * @property string $toi
 * @property string $ndr
 * @property string $gdr
 * @property int $avg_kunjungan
 */
class LaporanIndikatorRsView extends \Doco\components\DocoActiveRecord
{
/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl1_2_indikatorrs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tahun'], 'string'],
            [['bor', 'avlos', 'bto', 'toi', 'ndr', 'gdr'], 'number'],
            [['avg_kunjungan'], 'default', 'value' => null],
            [['avg_kunjungan'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tahun'         => 'Tahun',
            'bor'           => 'Bor',
            'avlos'         => 'Avlos',
            'bto'           => 'Bto',
            'toi'           => 'Toi',
            'ndr'           => 'Ndr',
            'gdr'           => 'Gdr',
            'avg_kunjungan' => 'Avg Kunjungan'
        ];
    }
}
