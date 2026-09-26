<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cetakstokopnamebarangdetail_v".
 *
 * @property int $stokopnamebarangdetail_id
 * @property int $stokopnamebarang_id
 * @property int $barang_id
 * @property int $ruangan_id
 * @property int $pegmengetahui_id
 * @property int $petugas_id
 * @property string $Tanggal Stok Opname
 * @property string $Nomor Stok Opname
 * @property string $Nama Barang
 * @property double $Jumlah Sistem
 * @property double $Jumlah Fisik
 * @property double $Harga Netto Sistem
 * @property double $Harga Netto Fisik
 * @property double $Selisih jumlah
 * @property double $Selisih Harga Netto
 */
class CetakStokOpnameBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cetakstokopnamebarangdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokopnamebarangdetail_id', 'stokopnamebarang_id', 'barang_id', 'ruangan_id', 'pegmengetahui_id', 'petugas_id'], 'default', 'value' => null],
            [['stokopnamebarangdetail_id', 'stokopnamebarang_id', 'barang_id', 'ruangan_id', 'pegmengetahui_id', 'petugas_id'], 'integer'],
            [['Tanggal Stok Opname'], 'safe'],
            [['Nomor Stok Opname'], 'string'],
            [['Jumlah Sistem', 'Jumlah Fisik', 'Harga Netto Sistem', 'Harga Netto Fisik', 'Selisih jumlah', 'Selisih Harga Netto'], 'number'],
            [['Nama Barang'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokopnamebarangdetail_id' => 'Stokopnamebarangdetail ID',
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'barang_id' => 'Barang ID',
            'ruangan_id' => 'Ruangan ID',
            'pegmengetahui_id' => 'Pegmengetahui ID',
            'petugas_id' => 'Petugas ID',
            'Tanggal Stok Opname' => 'Tanggal  Stok  Opname',
            'Nomor Stok Opname' => 'Nomor  Stok  Opname',
            'Nama Barang' => 'Nama  Barang',
            'Jumlah Sistem' => 'Jumlah  Sistem',
            'Jumlah Fisik' => 'Jumlah  Fisik',
            'Harga Netto Sistem' => 'Harga  Netto  Sistem',
            'Harga Netto Fisik' => 'Harga  Netto  Fisik',
            'Selisih jumlah' => 'Selisih Jumlah',
            'Selisih Harga Netto' => 'Selisih  Harga  Netto',
        ];
    }
}
