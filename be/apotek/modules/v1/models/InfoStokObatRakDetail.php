<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokobatrakdetail_v".
 *
 * @property int $obatalkes_id
 * @property double $stok_sistem
 * @property string $obatalkes_nama
 * @property string $tglkadaluarsa
 * @property double $harganetto
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $periodestokobat_id
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $sop_obatalkes_id
 * @property string $rakobat_nama
 * @property int $rakobat_id
 * @property int $laciobat_id
 */
class InfoStokObatRakDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infostokobatrakdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'ruangan_id', 'instalasi_id', 'sop_obatalkes_id', 'rakobat_id'], 'default', 'value' => null],
            [['obatalkes_id', 'ruangan_id', 'instalasi_id', 'sop_obatalkes_id', 'rakobat_id', 'laciobat_id'], 'integer'],
            [['stok_sistem', 'harganetto'], 'number'],
            [['tglkadaluarsa'], 'safe'],
            [['obatalkes_nama', 'rakobat_nama'], 'string', 'max' => 255],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'stok_sistem' => 'Stok Sistem',
            'obatalkes_nama' => 'Obatalkes Nama',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'harganetto' => 'Harganetto',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'sop_obatalkes_id' => 'Sop Obatalkes ID',
            'rakobat_id' => 'Rakobat ID',
            'laciobat_id' => 'Laciobat ID'
        ];
    }
}
