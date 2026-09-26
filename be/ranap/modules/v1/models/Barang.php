<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-23 17:29:36
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-23 17:30:05
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "barang_m".
 *
 * @property int $barang_id
 * @property int $golonganbarang_id lookup_m.lookup_type='golongan_barang'
 * @property int $kelompokbarang_id kelompokbarang_m
 * @property int $subkelompokbarang_id subkelompokbarang_m
 * @property string $barang_kode kode barang
 * @property string $barang_nama nama barang
 * @property string $barang_namalainnya nama lainnya
 * @property string $barang_merk merk
 * @property string $barang_image photo barang
 * @property double $barang_harganetto
 * @property double $barang_persendiskon
 * @property double $barang_ppn
 * @property double $barang_hpp
 * @property double $barang_hargajual
 * @property double $barang_min harga min
 * @property double $barang_max harga max
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
 * @property int $satuankecil_id satuanunit_m
 * @property int $satuan1_id satuanunit_m
 * @property int $satuan2_id satuanunit_m
 * @property double $barang_average harga average
 * @property string $barang_thnperoleh tahun perolehan
 * @property int $n_ekonomis_thn nilai ekonomis tahun
 * @property int $n_ekonomis_bln nilai ekonomis bulan
 * @property int $isi_satuan1 isi kemasaan satuan 1
 * @property int $isi_satuan2 isi kemasan satuan 2
 * @property bool $is_kadaluarsa ada kadaluarsa
 */
class Barang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'barang_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barang_kode', 'barang_nama', 'barang_merk', 'golonganbarang_id', 'kelompokbarang_id', 
            'subkelompokbarang_id', 'is_kadaluarsa', 'satuankecil_id', /*'satuan1_id', 'satuan2_id',*/ 
            /*'isi_satuan1', 'isi_satuan2',*/ /*'barang_thnperoleh', 'harga_perolehan', 'barang_harganetto'*/], 'required'],
            // [['barang_kode', 'barang_nama', 'barang_merk', 'golonganbarang_id', 'kelompokbarang_id', 
            // 'subkelompokbarang_id', 'is_kadaluarsa', 'satuankecil_id', 'satuan1_id', 'satuan2_id', 
            // 'isi_satuan1', 'isi_satuan2'], 'required'],
            [['golonganbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuankecil_id', 'n_ekonomis_thn', 'n_ekonomis_bln', 'isi_satuan1', 'isi_satuan2'], 'default', 'value' => null],
            [['golonganbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuankecil_id', 'satuan1_id', 'satuan2_id', 'n_ekonomis_thn', 'n_ekonomis_bln', 'isi_satuan1', 'isi_satuan2'], 'integer'],
            [['barang_harganetto', 'barang_persendiskon', 'barang_ppn', 'barang_hpp', 'barang_hargajual', 'barang_min', 'barang_max', 'barang_average'], 'number'],
            [['barang_harganetto'], 'default', 'value' => 0],
            [['additional_data'], 'string'],
            [['subkelompokbarang_id', 'nilai_ro', 'lead_time', 'avg_usage', 'min_order', 'max_order', 'stok_minimal', 'on_ro', 'on_po', 
            'harga_perolehan', 'created_date', 'last_modified_date', 'deleted_date', 
            'barang_thnperoleh'], 'safe'],
            [['is_deleted', 'is_active', 'is_kadaluarsa'], 'boolean'],
            [['barang_kode'], 'string', 'max' => 15],
            [['barang_merk'], 'string', 'max' => 30],
            [['barang_nama', 'barang_namalainnya'], 'string', 'max' => 30],
            [['barang_image'], 'string', 'max' => 200],
            [['barang_kode'], 'chkKode'],
            [['barang_nama'], 'chkNama'],
            ['isi_satuan1', 'required', 'when' => function($model) {
                return $model->satuan1_id != '';
            }],
            ['isi_satuan2', 'required', 'when' => function($model) {
                return $model->satuan2_id != '';
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => 'Barang ID',
            'golonganbarang_id' => 'Golongan',
            'kelompokbarang_id' => 'Kelompok',
            'subkelompokbarang_id' => 'Sub Kelompok',
            'barang_kode' => 'Kode Barang',
            'barang_nama' => 'Nama Barang',
            'barang_namalainnya' => 'Barang Namalainnya',
            'barang_merk' => 'Barang Merk',
            'barang_image' => 'Foto Barang',
            'barang_harganetto' => 'Harga Netto',
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
            'satuankecil_id' => 'Satuan Kecil',
            'satuan1_id' => 'Satuan 1',
            'satuan2_id' => 'Satuan 2',
            'barang_thnperoleh' => 'Tahun Perolehan',
            'n_ekonomis_thn' => 'Umur Ekonomis',
            'n_ekonomis_bln' => 'Umur Ekonomis',
            'isi_satuan1' => 'Isi Kemasan Satuan 1',
            'isi_satuan2' => 'Isi Kemasan Satuan 2',
            'is_kadaluarsa' => 'Ada Kadaluarsa',
            'avg_usage' => 'Average Usage',
            'min_order' => 'Minimum Order',
            'max_order' => 'Maximum Order',
            'nilai_ro' => 'Nilai RO',
        ];
    }

}