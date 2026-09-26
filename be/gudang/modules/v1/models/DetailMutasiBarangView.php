<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailmutasibarang_v".
 *
 * @property int $mutasibarangdetail_id
 * @property int $mutasibarang_id
 * @property string $nomutasi_barang
 * @property string $tgl_mutasibarang
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_nama
 * @property int $ruangan_tujuan_id
 * @property string $ruangan_nama
 * @property int $instalasi_asal_id
 * @property string $instalasi_asal
 * @property int $ruangan_asal_id
 * @property string $ruangan_asal
 * @property double $qty_mutasi
 * @property int $barang_id
 * @property string $barang_nama
 * @property int $satuanbrg
 * @property string $lookup_value
 * @property int $satuankecil_id
 * @property int $satuanbesar_id
 * @property string $satuankecil_nama
 * @property string $satuanbesar_nama
 */
class DetailMutasiBarangView extends \Doco\components\DocoActiveRecord
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
    public function rules()
    {
        return [
            [['mutasibarangdetail_id', 'mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'barang_id', 'satuanbrg', 'satuankecil_id', 'satuanbesar_id'], 'default', 'value' => null],
            [['mutasibarangdetail_id', 'mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'barang_id', 'satuanbrg', 'satuankecil_id', 'satuanbesar_id'], 'integer'],
            [['tgl_mutasibarang'], 'safe'],
            [['qty_mutasi'], 'number'],
            [['satuankecil_nama', 'satuanbesar_nama'], 'string'],
            [['nomutasi_barang', 'instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
            [['barang_nama'], 'string', 'max' => 100],
            [['lookup_value'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarangdetail_id' => 'Mutasibarangdetail ID',
            'mutasibarang_id' => 'Mutasibarang ID',
            'nomutasi_barang' => 'Nomutasi Barang',
            'tgl_mutasibarang' => 'Tgl Mutasibarang',
            'instalasi_tujuan_id' => 'Instalasi Tujuan ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_tujuan_id' => 'Ruangan Tujuan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_asal_id' => 'Instalasi Asal ID',
            'instalasi_asal' => 'Instalasi Asal',
            'ruangan_asal_id' => 'Ruangan Asal ID',
            'ruangan_asal' => 'Ruangan Asal',
            'qty_mutasi' => 'Qty Mutasi',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'satuanbrg' => 'Satuanbrg',
            'lookup_value' => 'Lookup Value',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuankecil_nama' => 'Satuankecil Nama',
            'satuanbesar_nama' => 'Satuanbesar Nama',
            'harga_netto' => 'Harga Netto'
        ];
    }
}
