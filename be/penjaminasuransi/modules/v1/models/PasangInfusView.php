<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi8_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasanginfus_id
 * @property int $jeniscairan_id
 * @property string $obatalkes_nama
 * @property string $tgl_pemasangan
 * @property int $jumlah_tetes
 */
class PasangInfusView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi8_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'pasanginfus_id', 'jeniscairan_id', 'jumlah_tetes'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'pasanginfus_id', 'jeniscairan_id', 'jumlah_tetes'], 'integer'],
            [['tgl_pemasangan'], 'safe'],
            [['obatalkes_nama'], 'string', 'max' => 255],
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
            'pasanginfus_id' => 'Pasanginfus ID',
            'jeniscairan_id' => 'Jeniscairan ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'tgl_pemasangan' => 'Tgl Pemasangan',
            'jumlah_tetes' => 'Jumlah Tetes',
        ];
    }
}
