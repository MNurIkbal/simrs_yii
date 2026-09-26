<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-05 13:38:43
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-07 14:46:34
 */

namespace app\modules\master\models;

use Yii;

class ObatAlkesForm extends \yii\base\Model
{
    public $obatalkes_nama;
    public $obatalkes_namalain;
    public $obatalkes_kode;
    public $jenisobatalkes_id;
    public $satuankecil_id;
    public $satuansedang_id;
    public $satuanbesar_id;
    public $kemasan_sedang;
    public $kemasan_besar;
    public $kekuatan_obat;
    public $satuankekuatan;
    public $is_generik;
    public $tglkadaluarsa;
    public $obatalkes_nobatch;
    public $obatalkes_kategori;
    public $ven;
    public $minimalstok;
    public $maksimalstok;
    public $supplier_id;
    public $supplier_ids;
    public $harganetto;
    public $harga_beli;
    public $hargamaksimum;
    public $hargaminimum;
    public $hargaratarata;
    public $discount;
    public $ppn_persen;
    public $indikasi;
    public $kontradiksi;
    public $interaksi;
    public $efek_samping;
    public $groupinacbg_id;
    public $lead_time;
    public $avg_usage;
    public $min_order;
    public $max_order;
    public $on_ro;
    public $on_po;
    public $hargaterakhir;
    public $is_formularium;
    public $is_oral;
    public $manufaktur_id;
    public $manufacture_ids;
    public $is_antibiotic;
    public $is_psycothropica;
    public $zataktif_id;
    public $is_narcotic;
    public $ruteobat_id;
    public $reorder;
    public $is_consigment;
    public $atccode_id;
    public $mims_id;
    public $obatalkesmims_id;
    public $is_produksi;

    public function rules()
    {
        return [
            [['obatalkes_nama','jenisobatalkes_id','satuankecil_id','is_generik',
                'harganetto', 'ven',
                'reorder',
                'is_consigment',
                'groupinacbg_id',
                'is_produksi',
                'ruteobat_id'
            ], 'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['satuanbesar_id', 'satuansedang_id', 'obatalkes_namalain', 'obatalkes_kode', 'obatalkes_nobatch','obatalkes_kategori','discount','hargamaksimum','hargaminimum','indikasi','interaksi','kontradiksi','efek_samping','kekuatan_obat','satuankekuatan','supplier_ids','supplier_id', 'hargaterakhir', 'is_formularium','is_oral','is_antibiotic','is_psycothropica','manufacture_ids', 'manufaktur_id', 'zataktif_id', 'is_narcotic', 'reorder', 'is_consigment', 'mims_id', 'obatalkesmims_id', 'is_produksi'], 'safe'],
            [['kemasan_besar', 'kemasan_sedang', 'minimalstok', 'maksimalstok', 'atccode_id'], 'integer'],
            [['kemasan_besar', 'kemasan_sedang'], 'number', 'min' => 1],
            [['reorder'], 'default', 'value' => 1],
            ['kemasan_besar', 'required', 'when' => function($model) {
                return $model->satuanbesar_id != '';
            }],
            ['obatalkesmims_id', 'required', 'when' => function($model) {
                return $model->mims_id != '';
            }],
            ['supplier_id', 'required', 'when' => function($model) {
                return $model->supplier_ids != null;
            }],
            ['manufaktur_id', 'required', 'when' => function($model) {
                return $model->manufacture_ids != null;
            }],
            [['is_consigment'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'obatalkes_nama' => Yii::t('fe', 'Nama obat alkes'),
            'obatalkes_namalain' => Yii::t('fe', 'Nama lain'),
            'obatalkes_kode' => Yii::t('fe', 'Kode obat alkes'),
            'jenisobatalkes_id' => Yii::t('fe', 'Jenis obat alkes'),
            'satuankecil_id' => Yii::t('fe','Satuan kecil'),
            'satuansedang_id' => Yii::t('fe','Satuan 2'),
            'satuanbesar_id' => Yii::t('fe','Satuan Besar'),
            'kemasan_sedang' => Yii::t('fe','Isi kemasan satuan 2'),
            'kemasan_besar' => Yii::t('fe','Isi kemasan satuan Besar'),
            'kekuatan_obat' => Yii::t('fe','Kekuatan'),
            'satuankekuatan' => '',
            'is_generik' => Yii::t('fe','Generik'),
            'obatalkes_nobatch' => Yii::t('fe','No batch'),
            'obatalkes_kategori' => Yii::t('fe','Kategori ABC'),
            'ven' => Yii::t('fe','Ven'),
            'minimalstok' => Yii::t('fe','Stok minimal'),
            'maksimalstok' => Yii::t('fe','Stok maksimal'),
            'supplier_id' => Yii::t('fe','Supplier Utama'),
            'supplier_ids' => Yii::t('fe','Supplier'),
            'harganetto' => Yii::t('fe','Harga Dasar'),
            'harga_beli' => Yii::t('fe','Harga beli'),
            'hargamaksimum' => Yii::t('fe','Harga jual maksimum'),
            'hargaminimum' => Yii::t('fe','Harga jual minimum'),
            'hargaratarata' => Yii::t('fe','Harga jual rata-rata'),
            'indikasi' => Yii::t('fe','Indikasi'),
            'kontradiksi' => Yii::t('fe','Kontradiksi'),
            'interaksi' => Yii::t('fe','Interaksi'),
            'efek_samping' => Yii::t('fe','Efek samping'),
            'avg_usage' => Yii::t('fe','Average Usage'),
            'min_order' => Yii::t('fe','Minimum Order'),
            'max_order' => Yii::t('fe','Maximum Order'),
            'groupinacbg_id' => Yii::t('fe','Group INA CBGS'),
            'hargaterakhir' => Yii::t('fe','Harga Terakhir'),
            'is_formularium' => Yii::t('fe','Formularium'),
            'is_antibiotic' => Yii::t('fe','Antibiotic'),
            'is_psycothropica' => Yii::t('fe','Pyscothropica'),
            'is_oral' => Yii::t('fe','Obat Oral'),
            'manufaktur_id' => Yii::t('fe','Manufaktur Utama'),
            'manufacture_ids' => Yii::t('fe','Manufaktur'),
            'zataktif_id' => Yii::t('fe','Zat Aktif'),
            'is_narcotic' => Yii::t('fe','Narcotics'),
            'ruteobat_id' => Yii::t('fe','Rute Obat'),
            'is_consigment' => Yii::t('fe','Consignment'),
            'atccode_id' => Yii::t('fe','Kode ATC'),
            'mims_id' => Yii::t('fe','MIMS'),
            'obatalkesmims_id' => Yii::t('fe','Sub MIMS'),
            'is_produksi' => Yii::t('fe','Obat Produksi'),
        ];
    }
}
