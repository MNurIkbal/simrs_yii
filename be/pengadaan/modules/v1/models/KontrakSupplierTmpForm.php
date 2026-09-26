<?php
namespace app\modules\v1\models;

use Yii;
use yii\base\Model;
use app\modules\v1\classes\ExcelColumn;

class KontrakSupplierTmpForm extends Model
{
    const DATERANGE_TYPE = 'daterange';
    const DATE_TYPE = 'date';
    const STRING_TYPE = 'string';
    const NUMBER_TYPE = 'number';
    const DATE_FORMAT = 'd M Y';
    const DATE_TIME = 'd M Y hh:mm:ss';
    const DATE_EXCEL = 'dateexcel';

    
    public $kontraksupplier_no;
    public $tgl_berlaku;
    public $kode_supplier;
    public $supplier;
    public $payterm;
    public $persen_ppn;
    public $contact_person;
    public $kode_obat;
    public $nama_obat;
    public $satuan_kecil;
    public $harga;
    public $diskon;
    public $qty_min;
    public $total_harga;
    public $status;
    public $keterangan;

    public $supplier_id;
    public $payterm_id;
    public $jumlah_hari;
    public $pajak_id;
    public $obatalkes_id;
    public $satuankecil_id;

    public function rules()
    {
        return [
            [['kontraksupplier_no', 'tgl_berlaku', 'kode_supplier', 'supplier', 'payterm', 'persen_ppn', 'contact_person', 'kode_obat', 'nama_obat', 'satuan_kecil', 'harga', 'diskon', 'qty_min', 'total_harga'], 'default', 'value' => null],
            [['kontraksupplier_no', 'kode_supplier', 'supplier', 'payterm', 'contact_person', 'kode_obat', 'nama_obat', 'satuan_kecil', 'keterangan'], 'string'],
            [['persen_ppn'], 'integer'],
            [['kontraksupplier_no', 'tgl_berlaku', 'kode_supplier', 'supplier', 'payterm', 'contact_person', 'kode_obat', 'nama_obat', 'harga', 'qty_min'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['qty_min'], 'required','message'=>'{attribute} Harus Lebih dari 0'],
            [['persen_ppn'], 'required','message'=>'PPN Tidak boleh kosong'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'kontraksupplier_no' => Yii::t('app', 'No Kontrak'),
            'tgl_berlaku' => Yii::t('app', 'Tgl Berlaku'),
            'kode_supplier' => Yii::t('app', 'Kode Supplier'),
            'supplier' => Yii::t('app', 'Supplier'),
            'payterm' => Yii::t('app', 'Payterm'),
            'persen_ppn' => Yii::t('app', 'persen_ppn'),
            'contact_person' => Yii::t('app', 'contact_person'),
            'kode_obat' => Yii::t('app', 'Kode Obat'),
            'nama_obat' => Yii::t('app', 'Nama Obat'),
            'satuan_kecil' => Yii::t('app', 'Satuan Kecil'),
            'harga' => Yii::t('app', 'Harga'),
            'diskon' => Yii::t('app', 'Diskon'),
            'qty_min' => Yii::t('app', 'Qty Min'),
            'total_harga' => Yii::t('app', 'Total Harga'),
        ];
    }

    public function columnNames() {
        return [
            (array) new ExcelColumn('kontraksupplier_no', self::STRING_TYPE, 'No Kontrak Supplier'),
            (array) new ExcelColumn('tgl_berlaku', self::DATE_EXCEL, 'Tgl Berlaku'),
            (array) new ExcelColumn('kode_supplier', self::STRING_TYPE, 'Kode Supplier'),
            (array) new ExcelColumn('supplier', self::STRING_TYPE, 'Supplier'),
            (array) new ExcelColumn('payterm', self::STRING_TYPE, 'Payterm'),
            (array) new ExcelColumn('persen_ppn', self::STRING_TYPE, 'ppn'),
            (array) new ExcelColumn('contact_person', self::STRING_TYPE, 'Contact Person'),
            (array) new ExcelColumn('kode_obat', self::STRING_TYPE, 'Kode Obat'),
            (array) new ExcelColumn('nama_obat', self::STRING_TYPE, 'Nama Obat (Mater Item)'),
            (array) new ExcelColumn('satuan_kecil', self::STRING_TYPE, 'Satuan Kecil'),
            (array) new ExcelColumn('Harga', self::STRING_TYPE, 'Harga'),
            (array) new ExcelColumn('diskon', self::STRING_TYPE, 'Diskon'),
            (array) new ExcelColumn('qty_min', self::STRING_TYPE, 'Qty Min'),
            (array) new ExcelColumn('total_harga', self::STRING_TYPE, 'Total Harga')
        ];
    }
}