<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomutasibarangdetail_v".
 *
 * @property int mutasibarangdetail_id
 * @property int mutasibarang_id
 * @property string nomutasi_barang
 * @property tgl_mutasibarang
 * @property int instalasi_tujuan_id
 * @property instalasi_nama
 * @property int ruangan_tujuan_id
 * @property string ruangan_nama
 * @property int instalasi_asal_id
 * @property string instalasi_asal
 * @property int ruangan_asal_id
 * @property string ruangan_asal
 * @property int qty_mutasi
 * @property int barang_id
 * @property string barang_nama
 * @property string satuanbrg
 * @property string lookup_value
 * @property int satuankecil_id
 * @property int satuanbesar_id
 * @property string satuankecil_nama
 * @property string satuanbesar_nama
 * @property int harga_netto
 */
class InfoMutasiBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infomutasibarangdetail_v';
    }

    /**
     * @inheritdoc
     */
    // public function rules()
    // {
    //     return [
    //         [['mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'status_mutasi'], 'default', 'value' => null],
    //         [['mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'status_mutasi'], 'integer'],
    //         [['tgl_mutasibarang'], 'safe'],
    //         [['nomutasi_barang', 'instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
    //         [['statusmutasi'], 'string', 'max' => 200],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarangdetail_id' => 'Mutasi Barang Detail ID' ,
            'mutasibarang_id' => 'Mutasi Barang ID' ,
            'nomutasi_barang' => 'No. Mutasi Barang' ,
            'tgl_mutasibarang' => 'Tanggal Mutasi' ,
            'instalasi_tujuan_id' => 'Instalasi Tujuan ID' ,
            'instalasi_nama' => 'Instalasi Tujuan' ,
            'ruangan_tujuan_id' => 'Ruangan Tujuan ID' ,
            'ruangan_nama' => 'Ruangan Tujuan' ,
            'instalasi_asal_id' => 'Instalasi Asal ID' ,
            'instalasi_asal' => 'Instalasi Asal' ,
            'ruangan_asal_id' => 'Ruangan Asal ID' ,
            'ruangan_asal' => 'Ruangan Asal' ,
            'qty_mutasi' => 'Qty Mutasi' ,
            'barang_id' => 'Barang ID' ,
            'barang_nama' => 'Nama Barang' ,
            'satuanbrg' => 'Satuan Barang' ,
            'lookup_value' => 'Lookup' ,
            'satuankecil_id' => 'Satuan Kecil ID' ,
            'satuanbesar_id' => 'Satuan Besar ID' ,
            'satuankecil_nama' => 'Satuan Kecil' ,
            'satuanbesar_nama' => 'Satuan Besar' ,
            'harga_netto' => 'Harga Netto' ,
        ];
    }
}
