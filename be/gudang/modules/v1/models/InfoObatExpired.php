<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoobatalkesexpired_v".
 *
 * @property int $obatalkes_id
 * @property double $stok
 * @property string $obatalkes_nama
 * @property string $satuan_kecil
 * @property string $tglkadaluarsa
 * @property double $harganetto
 * @property double $jumlah_harganetto
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $periodestokobat_id
 * @property string $tglperiodeposting_awal
 * @property string $tglperiodeposting_akhir
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $id_stok
 */
class InfoObatExpired extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoobatalkesexpired_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'periodestokobat_id', 'ruangan_id', 'instalasi_id', 'id_stok'], 'default', 'value' => null],
            [['obatalkes_id', 'periodestokobat_id', 'ruangan_id', 'instalasi_id', 'id_stok'], 'integer'],
            [['stok', 'harganetto', 'jumlah_harganetto'], 'number'],
            [['satuan_kecil'], 'string'],
            [['tglkadaluarsa', 'tglperiodeposting_awal', 'tglperiodeposting_akhir'], 'safe'],
            [['obatalkes_nama'], 'string', 'max' => 255],
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
            'stok' => 'Stok',
            'obatalkes_nama' => 'Obatalkes Nama',
            'satuan_kecil' => 'Satuan Kecil',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'harganetto' => 'Harganetto',
            'jumlah_harganetto' => 'Jumlah Harganetto',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'periodestokobat_id' => 'Periodestokobat ID',
            'tglperiodeposting_awal' => 'Tglperiodeposting Awal',
            'tglperiodeposting_akhir' => 'Tglperiodeposting Akhir',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'id_stok' => 'Id Stok',
        ];
    }
}
