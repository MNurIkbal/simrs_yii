<?php

namespace app\modules\v1\models;

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
 */
class Barang_backup extends \Doco\components\DocoActiveRecord
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
            [['kelompokbarang_id', 'subkelompokbarang_id', 'jenisbarang_id', 'barang_ekonomis_thn', 'barang_jmldlmkemasan'], 'default', 'value' => null],
            [['bidangbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id', 'jenisbarang_id', 'barang_ekonomis_thn', 'barang_jmldlmkemasan'], 'integer'],
            [['barang_type', 'barang_kode', 'barang_nama'], 'required'],
            [['barang_statusregister'], 'boolean'],
            [['barang_harganetto', 'barang_persendiskon', 'barang_ppn', 'barang_hpp', 'barang_hargajual', 'barang_min', 'barang_max'], 'number'],
            [['barang_type', 'barang_kode', 'barang_merk', 'barang_warna', 'barang_satuan'], 'string', 'max' => 50],
            [['barang_nama', 'barang_namalainnya'], 'string', 'max' => 100],
            [['barang_noseri', 'barang_ukuran', 'barang_bahan'], 'string', 'max' => 20],
            [['barang_thnbeli'], 'string', 'max' => 5],
            [['barang_image'], 'string', 'max' => 200],
            // [['bidangbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => BidangbarangM::className(), 'targetAttribute' => ['bidangbarang_id' => 'bidangbarang_id']],
            // [['jenisbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisbarangM::className(), 'targetAttribute' => ['jenisbarang_id' => 'jenisbarang_id']],
            // [['kelompokbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokbarangM::className(), 'targetAttribute' => ['kelompokbarang_id' => 'kelompokbarang_id']],
            // [['subkelompokbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubkelompokbarangM::className(), 'targetAttribute' => ['subkelompokbarang_id' => 'subkelompokbarang_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => Yii::t('app', 'Barang'),
            'bidangbarang_id' => Yii::t('app', 'Bidang barang'),
            'kelompokbarang_id' => Yii::t('app', 'Kelompok barang'),
            'subkelompokbarang_id' => Yii::t('app', 'Sub kelompok barang'),
            'jenisbarang_id' => Yii::t('app', 'Jenis barang'),
            'barang_type' => Yii::t('app', 'Type barang'),
            'barang_kode' => Yii::t('app', 'Kode barang'),
            'barang_nama' => Yii::t('app', 'Nama barang'),
            'barang_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'barang_merk' => Yii::t('app', 'Merk barang'),
            'barang_noseri' => Yii::t('app', 'Noseri barang'),
            'barang_ukuran' => Yii::t('app', 'Ukuran barang'),
            'barang_bahan' => Yii::t('app', 'Bahan barang'),
            'barang_thnbeli' => Yii::t('app', 'Tahun beli barang'),
            'barang_warna' => Yii::t('app', 'Warna barang'),
            'barang_statusregister' => Yii::t('app', 'Status register barang'),
            'barang_ekonomis_thn' => Yii::t('app', 'Tahun barang ekonomis'),
            'barang_satuan' => Yii::t('app', 'Satuan barang'),
            'barang_jmldlmkemasan' => Yii::t('app', 'Jumlah dalam kemasan b'),
            'barang_image' => Yii::t('app', 'Image barang'),
            'barang_harganetto' => Yii::t('app', 'Harga netto barang'),
            'barang_persendiskon' => Yii::t('app', 'Persen diskon barang'),
            'barang_ppn' => Yii::t('app', 'Ppn barang'),
            'barang_hpp' => Yii::t('app', 'Hpp barang'),
            'barang_hargajual' => Yii::t('app', 'Hargajual barang'),
            'barang_min' => Yii::t('app', 'Min barang'),
            'barang_max' => Yii::t('app', 'Max barang'),
        ];
    }
}
