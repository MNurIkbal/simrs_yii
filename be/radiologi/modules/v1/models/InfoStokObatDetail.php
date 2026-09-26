<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokobatdetail_v".
 *
 * @property int $obatalkes_id
 * @property double $stok_sistem
 * @property string $obatalkes_nama
 * @property string $nobatch
 * @property string $tglkadaluarsa
 * @property double $harganetto
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $periodestokobat_id
 * @property string $tglperiodeposting_awal
 * @property string $tglperiodeposting_akhir
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $sop_obatalkes_id
 * @property int $sop_stokopnamedetail_id
 */
class InfoStokObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infostokobatdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'periodestokobat_id', 'ruangan_id', 'instalasi_id', 'sop_obatalkes_id', 'sop_stokopnamedetail_id'], 'default', 'value' => null],
            [['obatalkes_id', 'periodestokobat_id', 'ruangan_id', 'instalasi_id', 'sop_obatalkes_id', 'sop_stokopnamedetail_id'], 'integer'],
            [['stok_sistem', 'harganetto'], 'number'],
            [['tglkadaluarsa', 'tglperiodeposting_awal', 'tglperiodeposting_akhir'], 'safe'],
            [['obatalkes_nama'], 'string', 'max' => 255],
            [['nobatch'], 'string', 'max' => 100],
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
            'nobatch' => 'Nobatch',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'harganetto' => 'Harganetto',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'periodestokobat_id' => 'Periodestokobat ID',
            'tglperiodeposting_awal' => 'Tglperiodeposting Awal',
            'tglperiodeposting_akhir' => 'Tglperiodeposting Akhir',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'sop_obatalkes_id' => 'Sop Obatalkes ID',
            'sop_stokopnamedetail_id' => 'Sop Stokopnamedetail ID',
        ];
    }
}
