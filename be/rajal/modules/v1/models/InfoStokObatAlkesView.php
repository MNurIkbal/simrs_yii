<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokobatalkes_v".
 *
 * @property int $periodestok_id
 * @property string $periodestok_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property string $obatalkes_kode
 * @property double $hargajual
 * @property int $satuankecil_id
 * @property string $satuanbesar_nama
 * @property int $satuansedang_id
 * @property string $satuansedang_nama
 * @property string $satuankecil_nama
 * @property int $satuanbesar_id
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_nama
 * @property double $ppn
 * @property double $harganetto
 * @property double $hargamaksimum
 * @property double $hargaminimum
 * @property double $hargaratarata
 * @property int $qty_masuk
 * @property int $qty_keluar
 * @property int $qty_dipesan
 * @property int $qty_tersedia
 * @property int $qty_stok
 * @property string $tglperiodestok_awal
 * @property string $tglperiodestok_akhir
 */
class InfoStokObatAlkesView extends \yii\db\ActiveRecord
{
    public $harga;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infostokobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'obatalkes_id', 'satuankecil_id', 'satuansedang_id', 'satuanbesar_id', 'jenisobatalkes_id', 'qty_masuk', 'qty_keluar', 'qty_dipesan', 'qty_tersedia', 'qty_stok'], 'default', 'value' => null],
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'obatalkes_id', 'satuankecil_id', 'satuansedang_id', 'satuanbesar_id', 'jenisobatalkes_id', 'qty_masuk', 'qty_keluar', 'qty_dipesan', 'qty_tersedia', 'qty_stok'], 'integer'],
            [['obatalkes_namalain', 'obatalkes_kode', 'satuanbesar_nama', 'satuansedang_nama', 'satuankecil_nama', 'jenisobatalkes_nama'], 'string'],
            [['hargajual', 'ppn', 'harganetto', 'hargamaksimum', 'hargaminimum', 'hargaratarata'], 'number'],
            [['tglperiodestok_awal', 'tglperiodestok_akhir'], 'safe'],
            [['periodestok_nama'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'periodestok_id' => 'Periodestok ID',
            'periodestok_nama' => 'Periodestok Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'obatalkes_kode' => 'Obatalkes Kode',
            'hargajual' => 'Hargajual',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanbesar_nama' => 'Satuanbesar Nama',
            'satuansedang_id' => 'Satuansedang ID',
            'satuansedang_nama' => 'Satuansedang Nama',
            'satuankecil_nama' => 'Satuankecil Nama',
            'satuanbesar_id' => 'Satuanbesar ID',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'ppn' => 'Ppn',
            'harganetto' => 'Harganetto',
            'hargamaksimum' => 'Hargamaksimum',
            'hargaminimum' => 'Hargaminimum',
            'hargaratarata' => 'Hargaratarata',
            'qty_masuk' => 'Qty Masuk',
            'qty_keluar' => 'Qty Keluar',
            'qty_dipesan' => 'Qty Dipesan',
            'qty_tersedia' => 'Qty Tersedia',
            'qty_stok' => 'Qty Stok',
            'tglperiodestok_awal' => 'Tglperiodestok Awal',
            'tglperiodestok_akhir' => 'Tglperiodestok Akhir',
        ];
    }
}
