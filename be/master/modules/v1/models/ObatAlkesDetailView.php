<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkesdetail_v".
 *
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property string $obatalkes_namalain
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_nama
 * @property int $ven_id
 * @property string $ven
 * @property string $obatalkes_kode
 * @property int $kekuatan_obat
 * @property int $satuankecil_id
 * @property string $satuan_kecil
 * @property int $satuansedang_id
 * @property string $satuan_1
 * @property int $satuanbesar_id
 * @property string $satuan_2
 * @property int $kemasan_sedang
 * @property int $kemasan_besar
 * @property bool $is_generik
 * @property string $tglkadaluarsa
 * @property string $obatalkes_nobatch
 * @property string $obatalkes_kategori
 * @property string $kategori
 * @property int $minimalstok
 * @property int $maksimalstok
 * @property int $supplier_id
 * @property string $supplier_nama
 * @property double $harga_beli
 * @property double $discount
 * @property double $ppn_persen
 * @property double $harganetto
 * @property double $hargamaksimum
 * @property double $hargaminimum
 * @property double $hargaratarata
 * @property string $indikasi
 * @property string $interaksi
 * @property string $kontradiksi
 * @property string $efek_samping
 */
class ObatAlkesDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'obatalkesdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'jenisobatalkes_id', 'ven_id', 'kekuatan_obat', 'satuankecil_id', 'satuansedang_id', 'satuanbesar_id', 'kemasan_sedang', 'kemasan_besar', 'minimalstok', 'maksimalstok', 'supplier_id'], 'default', 'value' => null],
            [['obatalkes_id', 'jenisobatalkes_id', 'ven_id', 'kekuatan_obat', 'satuankecil_id', 'satuansedang_id', 'satuanbesar_id', 'kemasan_sedang', 'kemasan_besar', 'minimalstok', 'maksimalstok', 'supplier_id'], 'integer'],
            [['obatalkes_namalain', 'jenisobatalkes_nama', 'obatalkes_kode', 'satuan_kecil', 'satuan_1', 'satuan_2', 'obatalkes_nobatch', 'obatalkes_kategori', 'indikasi', 'interaksi', 'kontradiksi', 'efek_samping'], 'string'],
            [['is_generik'], 'boolean'],
            [['on_ro', 'on_po', 'tglkadaluarsa'], 'safe'],
            [['harga_beli', 'discount', 'ppn_persen', 'harganetto', 'hargamaksimum', 'hargaminimum', 'hargaratarata'], 'number'],
            [['obatalkes_nama'], 'string', 'max' => 255],
            [['ven', 'kategori'], 'string', 'max' => 200],
            [['supplier_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'ven_id' => 'Ven ID',
            'ven' => 'Ven',
            'obatalkes_kode' => 'Obatalkes Kode',
            'kekuatan_obat' => 'Kekuatan Obat',
            'satuankecil_id' => 'Satuankecil ID',
            'satuan_kecil' => 'Satuan Kecil',
            'satuansedang_id' => 'Satuansedang ID',
            'satuan_1' => 'Satuan 1',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuan_2' => 'Satuan 2',
            'kemasan_sedang' => 'Kemasan Sedang',
            'kemasan_besar' => 'Kemasan Besar',
            'is_generik' => 'Is Generik',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'obatalkes_nobatch' => 'Obatalkes Nobatch',
            'obatalkes_kategori' => 'Obatalkes Kategori',
            'kategori' => 'Kategori',
            'minimalstok' => 'Minimalstok',
            'maksimalstok' => 'Maksimalstok',
            'supplier_id' => 'Supplier ID',
            'supplier_nama' => 'Supplier Nama',
            'harga_beli' => 'Harga Beli',
            'discount' => 'Discount',
            'ppn_persen' => 'Ppn Persen',
            'harganetto' => 'Harganetto',
            'hargamaksimum' => 'Hargamaksimum',
            'hargaminimum' => 'Hargaminimum',
            'hargaratarata' => 'Hargaratarata',
            'indikasi' => 'Indikasi',
            'interaksi' => 'Interaksi',
            'kontradiksi' => 'Kontradiksi',
            'efek_samping' => 'Efek Samping',
        ];
    }
}
