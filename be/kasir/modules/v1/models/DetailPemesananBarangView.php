<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailpemesananbarang_v".
 *
 * @property int $pesanbarangdetail_id
 * @property int $pesanbarang_id
 * @property string $tgl_pesanbarang
 * @property string $no_pemesanan
 * @property int $ruanganpemesan_id
 * @property string $ruangan_pemesan
 * @property int $ruangantujuan_id
 * @property string $ruangan_tujuan
 * @property int $barang_id
 * @property string $barang_nama
 * @property double $qty_pesan
 * @property string $satuanbarang
 * @property string $tgl_mintadikirim
 */
class DetailPemesananBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailpemesananbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanbarangdetail_id', 'pesanbarang_id', 'ruanganpemesan_id', 'ruangantujuan_id', 'barang_id'], 'default', 'value' => null],
            [['pesanbarangdetail_id', 'pesanbarang_id', 'ruanganpemesan_id', 'ruangantujuan_id', 'barang_id'], 'integer'],
            [['tgl_pesanbarang', 'tgl_mintadikirim'], 'safe'],
            [['qty_pesan'], 'number'],
            [['no_pemesanan', 'ruangan_pemesan', 'ruangan_tujuan', 'satuanbarang'], 'string', 'max' => 50],
            [['barang_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarangdetail_id' => 'Pesanbarangdetail ID',
            'pesanbarang_id' => 'Pesanbarang ID',
            'tgl_pesanbarang' => 'Tgl Pesanbarang',
            'no_pemesanan' => 'No Pemesanan',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'ruangan_pemesan' => 'Ruangan Pemesan',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'qty_pesan' => 'Qty Pesan',
            'satuanbarang' => 'Satuanbarang',
            'tgl_mintadikirim' => 'Tgl Mintadikirim',
            'satuan_besar' => 'Satuan Besar',
            'satuan_kecil' => 'Satuan Kecil',
            'satuanbesar_id' => 'Satuan Besar ID',
            'satuankecil_id' => 'Satuan Kecil ID',
            'harga_netto' => 'Harga Netto',
        ];
    }
}
