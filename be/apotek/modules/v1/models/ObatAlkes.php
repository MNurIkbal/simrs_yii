<?php

namespace app\modules\v1\models;
use app\modules\v1\models\Supplier;

use Yii;

/**
 * This is the model class for table "obatalkes_m".
 *
 * @property integer $obatalkes_id
 * @property integer $jenisobatalkes_id
 * @property integer $sumberdana_id
 * @property integer $lokasigudang_id
 * @property integer $satuankecil_id
 * @property integer $satuanbesar_id
 * @property string $obatalkes_barcode
 * @property string $obatalkes_kode
 * @property string $obatalkes_namalain
 * @property string $obatalkes_kategori
 * @property string $obatalkes_kadarobat
 * @property string $satuankekuatan
 * @property double $ppn_persen
 * @property double $harganetto
 * @property double $hargajual
 * @property double $hargamaksimum
 * @property double $hargaminimum
 * @property double $hargaratarata
 * @property double $discount
 * @property string $tglkadaluarsa
 * @property integer $minimalstok
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property DiagnosaobatMp[] $diagnosaobatMps
 * @property DiagnosaM[] $diagnosas
 * @property FakturdetailT[] $fakturdetailTs
 * @property KasuspenyakitobatMp[] $kasuspenyakitobatMps
 * @property GenerikM $generik
 * @property JenisobatalkesM $jenisobatalkes
 * @property LokasigudangM $lokasigudang
 * @property SatuanbesarM $satuanbesar
 * @property SatuankecilM $satuankecil
 * @property SubjenisM $subjenis
 * @property SumberdanaM $sumberdana
 * @property ObatalkesdetailM[] $obatalkesdetailMs
 * @property ObatalkesproduksiM[] $obatalkesproduksiMs
 * @property ObatsupplierM[] $obatsupplierMs
 * @property PaketbmhpM[] $paketbmhpMs
 * @property PenerimaandetailT[] $penerimaandetailTs
 * @property PermintaandetailT[] $permintaandetailTs
 * @property TherapiobatalkesMp[] $therapiobatalkesMps
 * @property TherapiobatM[] $therapiobats
 */
class ObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'obatalkes_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id'], 'required'],
            [['obatalkes_id', 'jenisobatalkes_id', 'sumberdana_id', 'lokasigudang_id', 'satuankecil_id', 'satuanbesar_id',   'minimalstok', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['obatalkes_barcode', 'obatalkes_kode', 'obatalkes_namalain', 'obatalkes_kategori', 'obatalkes_kadarobat', 'satuankekuatan', 'additional_data'], 'string'],
            [['ppn_persen', 'harganetto', 'hargajual', 'hargamaksimum', 'hargaminimum', 'hargaratarata', 'discount'], 'number'],
            [['tglkadaluarsa', 'created_date', 'last_modified_date', 'deleted_date', 'ruangan_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['generik_id'], 'exist', 'skipOnError' => true, 'targetClass' => Generik::className(), 'targetAttribute' => ['generik_id' => 'generik_id']],
            [['jenisobatalkes_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisObatAlkes::className(), 'targetAttribute' => ['jenisobatalkes_id' => 'jenisobatalkes_id']],
            [['lokasigudang_id'], 'exist', 'skipOnError' => true, 'targetClass' => LokasiGudang::className(), 'targetAttribute' => ['lokasigudang_id' => 'lokasigudang_id']],
            // [['satuanbesar_id'], 'exist', 'skipOnError' => true, 'targetClass' => SatuanBesar::className(), 'targetAttribute' => ['satuanbesar_id' => 'satuanbesar_id']],
            // [['satuankecil_id'], 'exist', 'skipOnError' => true, 'targetClass' => SatuanKecil::className(), 'targetAttribute' => ['satuankecil_id' => 'satuankecil_id']],
            // [['exist', 'skipOnError' => true, 'targetClass' => SubJenis::className()]],
            // [['sumberdana_id'], 'exist', 'skipOnError' => true, 'targetClass' => SumberDana::className(), 'targetAttribute' => ['sumberdana_id' => 'sumberdana_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'sumberdana_id' => 'Sumberdana ID',
            'lokasigudang_id' => 'Lokasigudang ID',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            // 'generik_id' => 'Generik ID',
            'obatalkes_barcode' => 'Obatalkes Barcode',
            'obatalkes_kode' => 'Obatalkes Kode',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'obatalkes_kategori' => 'Obatalkes Kategori',
            'obatalkes_kadarobat' => 'Obatalkes Kadarobat',
            'satuankekuatan' => 'Satuankekuatan',
            'ppn_persen' => 'Ppn Persen',
            'harganetto' => 'Harganetto',
            'hargajual' => 'Hargajual',
            'hargamaksimum' => 'Hargamaksimum',
            'hargaminimum' => 'Hargaminimum',
            'hargaratarata' => 'Hargaratarata',
            'discount' => 'Discount',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'minimalstok' => 'Minimalstok',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosaobatMps()
    {
        return $this->hasMany(DiagnosaOba::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosas()
    {
        return $this->hasMany(Diagnosa::className(), ['diagnosa_id' => 'diagnosa_id'])->viaTable('diagnosaobat_mp', ['obatalkes_id' => 'obatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFakturdetailTs()
    {
        return $this->hasMany(FakturDetail::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKasuspenyakitobatMps()
    {
        return $this->hasMany(KasusPenyakitObat::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getGenerik()
    // {
    //     return $this->hasOne(Generik::className(), ['generik_id' => 'generik_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisobatalkes()
    {
        return $this->hasOne(JenisObatAlkes::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLokasigudang()
    {
        return $this->hasOne(LokasiGudang::className(), ['lokasigudang_id' => 'lokasigudang_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['supplier_id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getSatuanbesar()
    // {
    //     return $this->hasOne(SatuanBesar::className(), ['satuanbesar_id' => 'satuanbesar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getSatuankecil()
    // {
    //     return $this->hasOne(SatuanKecil::className(), ['satuankecil_id' => 'satuankecil_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getSubjenis()
    // {
    //     return $this->hasOne(SubJenis::className(), ['subjenis_id' => 'subjenis_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSumberdana()
    // {
    //     return $this->hasOne(SumberDana::className(), ['sumberdana_id' => 'sumberdana_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getObatalkesdetailMs()
    // {
    //     return $this->hasMany(ObatAlkesDetail::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getObatalkesproduksiMs()
    // {
    //     return $this->hasMany(ObatAlkesProduksi::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getObatsupplierMs()
    // {
    //     return $this->hasMany(ObatSupplier::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPaketbmhpMs()
    // {
    //     return $this->hasMany(PaketBmhp::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaandetailTs()
    // {
    //     return $this->hasMany(PenerimaanDetail::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPermintaandetailTs()
    // {
    //     return $this->hasMany(PermintaanDetail::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTherapiobatalkesMps()
    // {
    //     return $this->hasMany(TherapiObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTherapiobats()
    // {
    //     return $this->hasMany(TherapiObat::className(), ['therapiobat_id' => 'therapiobat_id'])->viaTable('therapiobatalkes_mp', ['obatalkes_id' => 'obatalkes_id']);
    // }
}
