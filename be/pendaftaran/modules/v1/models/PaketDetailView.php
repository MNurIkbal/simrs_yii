<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketdetail_v".
 *
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property string $daftartindakan_nama
 */
class PaketDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paketdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipepaket_id'], 'default', 'value' => null],
            [['tipepaket_id'], 'integer'],
            [['tipepaket_nama'], 'string', 'max' => 50],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'daftartindakan_nama' => 'Daftartindakan Nama',
        ];
    }
}
