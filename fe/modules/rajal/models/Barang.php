<?php

namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "barang_m".
 *
 * @property int $barang_id
 * @property int $bidangbarang_id
 * @property int $kelompokbarang_id
 * @property int $subkelompokbarang_id
 * @property int $jenisbarang_id
 * @property string $barang_type
 * @property string $barang_kode
 * @property string $barang_nama
 * @property string $barang_namalainnya
 * @property string $barang_merk
 * @property string $barang_noseri
 * @property string $barang_ukuran
 * @property string $barang_bahan
 * @property string $barang_thnbeli
 * @property string $barang_warna
 * @property bool $barang_statusregister
 * @property int $barang_ekonomis_thn
 * @property string $barang_satuan
 * @property int $barang_jmldlmkemasan
 * @property string $barang_image
 * @property double $barang_harganetto
 * @property double $barang_persendiskon
 * @property double $barang_ppn
 * @property double $barang_hpp
 * @property double $barang_hargajual
 * @property double $barang_min
 * @property double $barang_max
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property BidangbarangM $bidangbarang
 * @property JenisbarangM $jenisbarang
 * @property KelompokbarangM $kelompokbarang
 * @property SubkelompokbarangM $subkelompokbarang
 * @property BatalmutasibarangT[] $batalmutasibarangTs
 * @property BelibarangdetailT[] $belibarangdetailTs
 * @property DekontaminasidetailT[] $dekontaminasidetailTs
 * @property InventarisasiasetlainT[] $inventarisasiasetlainTs
 * @property InventarisasibarangdetailT[] $inventarisasibarangdetailTs
 * @property InventarisasigedungT[] $inventarisasigedungTs
 * @property InventarisasijalanT[] $inventarisasijalanTs
 * @property InventarisasiperalatanT[] $inventarisasiperalatanTs
 * @property InventarisasiruanganT[] $inventarisasiruanganTs
 * @property InventarisasitanahT[] $inventarisasitanahTs
 * @property LinenM[] $linenMs
 * @property MutasibarangdetailT[] $mutasibarangdetailTs
 * @property TerimapersediaandetailT[] $terimapersediaandetailTs
 */
class Barang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'barang_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bidangbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id', 'jenisbarang_id', 'barang_ekonomis_thn', 'barang_jmldlmkemasan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['bidangbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id', 'jenisbarang_id', 'barang_ekonomis_thn', 'barang_jmldlmkemasan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['barang_type', 'barang_kode', 'barang_nama'], 'required'],
            [['barang_statusregister', 'is_deleted', 'is_active'], 'boolean'],
            [['barang_harganetto', 'barang_persendiskon', 'barang_ppn', 'barang_hpp', 'barang_hargajual', 'barang_min', 'barang_max'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['barang_type', 'barang_kode', 'barang_merk', 'barang_warna', 'barang_satuan'], 'string', 'max' => 50],
            [['barang_nama', 'barang_namalainnya'], 'string', 'max' => 100],
            [['barang_noseri', 'barang_ukuran', 'barang_bahan'], 'string', 'max' => 20],
            [['barang_thnbeli'], 'string', 'max' => 5],
            [['barang_image'], 'string', 'max' => 200],
            [['bidangbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => BidangbarangM::className(), 'targetAttribute' => ['bidangbarang_id' => 'bidangbarang_id']],
            [['jenisbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisbarangM::className(), 'targetAttribute' => ['jenisbarang_id' => 'jenisbarang_id']],
            [['kelompokbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokbarangM::className(), 'targetAttribute' => ['kelompokbarang_id' => 'kelompokbarang_id']],
            [['subkelompokbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubkelompokbarangM::className(), 'targetAttribute' => ['subkelompokbarang_id' => 'subkelompokbarang_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => 'Barang ID',
            'bidangbarang_id' => 'Bidangbarang ID',
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'subkelompokbarang_id' => 'Subkelompokbarang ID',
            'jenisbarang_id' => 'Jenisbarang ID',
            'barang_type' => 'Barang Type',
            'barang_kode' => 'Barang Kode',
            'barang_nama' => 'Barang Nama',
            'barang_namalainnya' => 'Barang Namalainnya',
            'barang_merk' => 'Barang Merk',
            'barang_noseri' => 'Barang Noseri',
            'barang_ukuran' => 'Barang Ukuran',
            'barang_bahan' => 'Barang Bahan',
            'barang_thnbeli' => 'Barang Thnbeli',
            'barang_warna' => 'Barang Warna',
            'barang_statusregister' => 'Barang Statusregister',
            'barang_ekonomis_thn' => 'Barang Ekonomis Thn',
            'barang_satuan' => 'Barang Satuan',
            'barang_jmldlmkemasan' => 'Barang Jmldlmkemasan',
            'barang_image' => 'Barang Image',
            'barang_harganetto' => 'Barang Harganetto',
            'barang_persendiskon' => 'Barang Persendiskon',
            'barang_ppn' => 'Barang Ppn',
            'barang_hpp' => 'Barang Hpp',
            'barang_hargajual' => 'Barang Hargajual',
            'barang_min' => 'Barang Min',
            'barang_max' => 'Barang Max',
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
    public function getBidangbarang()
    {
        return $this->hasOne(BidangbarangM::className(), ['bidangbarang_id' => 'bidangbarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisbarang()
    {
        return $this->hasOne(JenisbarangM::className(), ['jenisbarang_id' => 'jenisbarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokbarang()
    {
        return $this->hasOne(KelompokbarangM::className(), ['kelompokbarang_id' => 'kelompokbarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubkelompokbarang()
    {
        return $this->hasOne(SubkelompokbarangM::className(), ['subkelompokbarang_id' => 'subkelompokbarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatalmutasibarangTs()
    {
        return $this->hasMany(BatalmutasibarangT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBelibarangdetailTs()
    {
        return $this->hasMany(BelibarangdetailT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDekontaminasidetailTs()
    {
        return $this->hasMany(DekontaminasidetailT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasiasetlainTs()
    {
        return $this->hasMany(InventarisasiasetlainT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasibarangdetailTs()
    {
        return $this->hasMany(InventarisasibarangdetailT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasigedungTs()
    {
        return $this->hasMany(InventarisasigedungT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasijalanTs()
    {
        return $this->hasMany(InventarisasijalanT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasiperalatanTs()
    {
        return $this->hasMany(InventarisasiperalatanT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasiruanganTs()
    {
        return $this->hasMany(InventarisasiruanganT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasitanahTs()
    {
        return $this->hasMany(InventarisasitanahT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinenMs()
    {
        return $this->hasMany(LinenM::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibarangdetailTs()
    {
        return $this->hasMany(MutasibarangdetailT::className(), ['barang_id' => 'barang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerimapersediaandetailTs()
    {
        return $this->hasMany(TerimapersediaandetailT::className(), ['barang_id' => 'barang_id']);
    }
}
