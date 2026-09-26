<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cetakjadwalpoli_v".
 *
 * @property int $ruangan_id
 * @property string $Nama Ruangan
 * @property string $Hari
 * @property string $Jam Buka
 * @property string $Jam Tutup
 * @property bool $Status
 */
class CetakJadwalPoliView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cetakjadwalpoli_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id'], 'default', 'value' => null],
            [['ruangan_id'], 'integer'],
            [['Jam Buka', 'Jam Tutup'], 'safe'],
            [['Status'], 'boolean'],
            [['Nama Ruangan'], 'string', 'max' => 50],
            [['Hari'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'Nama Ruangan' => 'Nama  Ruangan',
            'Hari' => 'Hari',
            'Jam Buka' => 'Jam  Buka',
            'Jam Tutup' => 'Jam  Tutup',
            'Status' => 'Status',
        ];
    }
}
