<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "infotarifrs_v".
 *
 * @property int $tariftindakan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruanganpaket_id
 * @property string $ruanganpaket_nama
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property int $kategoritindakan_id
 * @property string $kategoritindakan_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property int $persencyto_tindakan
 * @property int $persendiskon_tindakan
 * @property bool $is_default
 */
class InfoTarifrs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotarifrs_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariftindakan_id', 'ruangan_id', 'instalasi_id', 'ruanganpaket_id', 'perdatarif_id', 'kelaspelayanan_id', 'penjamin_id', 'kelompoktindakan_id', 'kategoritindakan_id', 'daftartindakan_id', 'tipepaket_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan'], 'default', 'value' => null],
            [['tariftindakan_id', 'ruangan_id', 'instalasi_id', 'ruanganpaket_id', 'perdatarif_id', 'kelaspelayanan_id', 'penjamin_id', 'kelompoktindakan_id', 'kategoritindakan_id', 'daftartindakan_id', 'tipepaket_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan'], 'integer'],
            [['ruanganpaket_nama', 'kelompoktindakan_nama', 'kategoritindakan_nama', 'daftartindakan_nama', 'tipepaket_nama'], 'string'],
            [['harga_tariftindakan'], 'number'],
            [['is_default'], 'boolean'],
            [['ruangan_nama', 'instalasi_nama', 'kelaspelayanan_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['perdanama_sk'], 'string', 'max' => 200],
            [['komponentarif_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tariftindakan_id' => 'Tariftindakan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruanganpaket_id' => 'Ruanganpaket ID',
            'ruanganpaket_nama' => 'Ruanganpaket Nama',
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kategoritindakan_nama' => 'Kategoritindakan Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'is_default' => 'Is Default',
        ];
    }
}
