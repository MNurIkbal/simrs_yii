<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoobatalkes_v".
 *
 * @property int $obatalkes_id
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_nama
 * @property int $satuankecil_id
 * @property string $satuan_kecil
 * @property int $satuansedang_id
 * @property string $satuan_sedang
 * @property int $satuanbesar_id
 * @property string $satuan_besar
 * @property int $kemasan_besar
 * @property int $kemasan_sedang
 * @property int $ven
 * @property string $ven_nama
 * @property string $obatalkes_barcode
 * @property string $obatalkes_kode
 * @property string $obatalkes_nama
 * @property string $obatalkes_namalain
 * @property string $obatalkes_nobatch
 * @property int $kekuatan_obat
 * @property string $satuan_kekuatan
 * @property double $ppn_persen
 * @property double $harganetto
 * @property double $hargajual
 * @property double $hargamaksimum
 * @property double $hargaminimum
 * @property double $hargaratarata
 * @property double $discount
 * @property string $tglkadaluarsa
 * @property int $minimalstok
 * @property bool $is_generik
 * @property bool $is_formularium
 * @property int $supplier_id
 * @property string $supplier_nama
 * @property string $indikasi
 * @property string $kontradiksi
 * @property string $interaksi
 * @property string $efek_samping
 * @property int $on_po
 * @property int $on_ro
 */
class InfoObatAlkesView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoobatalkes_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'jenisobatalkes_id', 'satuankecil_id', 'satuansedang_id', 'satuanbesar_id', 'kemasan_besar', 'kemasan_sedang', 'ven', 'kekuatan_obat', 'minimalstok', 'supplier_id', 'on_po', 'on_ro'], 'default', 'value' => null],
            [['obatalkes_id', 'jenisobatalkes_id', 'satuankecil_id', 'satuansedang_id', 'satuanbesar_id', 'kemasan_besar', 'kemasan_sedang', 'ven', 'kekuatan_obat', 'minimalstok', 'supplier_id', 'on_po', 'on_ro'], 'integer'],
            [['jenisobatalkes_nama', 'satuan_kecil', 'satuan_sedang', 'satuan_besar', 'obatalkes_barcode', 'obatalkes_kode', 'obatalkes_namalain', 'obatalkes_nobatch', 'indikasi', 'kontradiksi', 'interaksi', 'efek_samping'], 'string'],
            [['ppn_persen', 'harganetto', 'hargajual', 'hargamaksimum', 'hargaminimum', 'hargaratarata', 'discount'], 'number'],
            [['tglkadaluarsa'], 'safe'],
            [['is_generik', 'is_formularium'], 'boolean'],
            [['ven_nama', 'satuan_kekuatan'], 'string', 'max' => 200],
            [['obatalkes_nama'], 'string', 'max' => 255],
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
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'satuankecil_id' => 'Satuankecil ID',
            'satuan_kecil' => 'Satuan Kecil',
            'satuansedang_id' => 'Satuansedang ID',
            'satuan_sedang' => 'Satuan Sedang',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuan_besar' => 'Satuan Besar',
            'kemasan_besar' => 'Kemasan Besar',
            'kemasan_sedang' => 'Kemasan Sedang',
            'ven' => 'Ven',
            'ven_nama' => 'Ven Nama',
            'obatalkes_barcode' => 'Obatalkes Barcode',
            'obatalkes_kode' => 'Obatalkes Kode',
            'obatalkes_nama' => 'Obatalkes Nama',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'obatalkes_nobatch' => 'Obatalkes Nobatch',
            'kekuatan_obat' => 'Kekuatan Obat',
            'satuan_kekuatan' => 'Satuan Kekuatan',
            'ppn_persen' => 'Ppn Persen',
            'harganetto' => 'Harganetto',
            'hargajual' => 'Hargajual',
            'hargamaksimum' => 'Hargamaksimum',
            'hargaminimum' => 'Hargaminimum',
            'hargaratarata' => 'Hargaratarata',
            'discount' => 'Discount',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'minimalstok' => 'Minimalstok',
            'is_generik' => 'Is Generik',
            'is_formularium' => 'Is Formularium',
            'supplier_id' => 'Supplier ID',
            'supplier_nama' => 'Supplier Nama',
            'indikasi' => 'Indikasi',
            'kontradiksi' => 'Kontradiksi',
            'interaksi' => 'Interaksi',
            'efek_samping' => 'Efek Samping',
            'on_po' => 'On Po',
            'on_ro' => 'On Ro',
        ];
    }
}
