<?php

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
class MasterBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'barang_m';
    }
}
