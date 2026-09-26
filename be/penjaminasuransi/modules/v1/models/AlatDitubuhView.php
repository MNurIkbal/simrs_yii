<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi5_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $alatditubuh_id
 * @property int $jenis_alat
 * @property string $j_alat
 * @property int $jumlah
 * @property string $lokasi_alat
 */
class AlatDitubuhView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi5_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'alatditubuh_id', 'jenis_alat', 'jumlah'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'alatditubuh_id', 'jenis_alat', 'jumlah'], 'integer'],
            [['j_alat', 'lokasi_alat'], 'string', 'max' => 255],
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
            'alatditubuh_id' => 'Alatditubuh ID',
            'jenis_alat' => 'Jenis Alat',
            'j_alat' => 'J Alat',
            'jumlah' => 'Jumlah',
            'lokasi_alat' => 'Lokasi Alat',
        ];
    }
}
