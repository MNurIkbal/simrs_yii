<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infokartustokbarang_v".
 *
 * @property int $stokbarang_id
 * @property string $tglstok_in
 * @property string $tglstok_out
 * @property int $barang_id
 * @property string $barang_nama
 * @property string $penerimaandetail_id
 * @property string $no_penerimaan
 * @property int $gudangpenerima_id
 * @property string $ruangan_penerimaan
 * @property string $terimamutasibarangdetail_id
 * @property string $noterimamutasi
 * @property int $ruanganasal_id1
 * @property int $ruanganpenerima_id
 * @property string $ruangan_asal_terimamutasi
 * @property string $ruangan_tujuan_terimamutasi
 * @property string $returbarangdetail_id
 * @property string $no_returpenerimaanbarang
 * @property int $ruanganretur_id
 * @property string $ruangan_returpenerimaan
 * @property string $mutasibarangdetail_id
 * @property string $nomutasi_barang
 * @property int $ruanganasal_id2
 * @property int $ruangantujuan_id
 * @property string $ruangan_asal_mutasi
 * @property string $ruangan_tujuan_mutasi
 * @property string $pemusnahanbarangdetail_id
 * @property string $nopemusnahan
 * @property int $ruangan_id3
 * @property string $ruangan_pemusnahan
 * @property string $stokopnamebarangdetail_id
 * @property string $nostokopname
 * @property int $ruangan_id4
 * @property string $ruangan_stokopname
 * @property string $pemakaianbarangdetail_id
 * @property string $no_pemakaianbarang
 * @property int $ruangan_id5
 * @property string $ruangan_pemakaianbarang
 * @property string $produksibarangdetail_id
 * @property string $no_produksibarang
 * @property int $ruangan_id6
 * @property string $ruangan_produksibarang
 * @property string $storexpiredbarangdetail_id
 * @property string $no_storexpired
 * @property int $ruangan_id7
 * @property string $ruangan_storexpiredbarang
 * @property double $qtystok_in
 * @property double $qtystok_out
 * @property double $stok
 * @property int $satuankecil_id
 * @property string $satuanunit_nama
 * @property string $tglkadaluarsa
 * @property string $keterangan
 * @property string $tanggal_transaksi
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $ruangan_asal
 * @property string $ruangan_asal_nama
 * @property string $ruangan_tujuan
 * @property string $ruangan_tujuan_nama
 */
class InfoKartuStokBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infokartustokbarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokbarang_id', 'barang_id', 'gudangpenerima_id', 'ruanganasal_id1', 'ruanganpenerima_id', 'ruanganretur_id', 'ruanganasal_id2', 'ruangantujuan_id', 'ruangan_id3', 'ruangan_id4', 'ruangan_id5', 'ruangan_id6', 'ruangan_id7', 'satuankecil_id', 'ruangan_id'], 'default', 'value' => null],
            [['stokbarang_id', 'barang_id', 'gudangpenerima_id', 'ruanganasal_id1', 'ruanganpenerima_id', 'ruanganretur_id', 'ruanganasal_id2', 'ruangantujuan_id', 'ruangan_id3', 'ruangan_id4', 'ruangan_id5', 'ruangan_id6', 'ruangan_id7', 'satuankecil_id', 'ruangan_id'], 'integer'],
            [['tglstok_in', 'tglstok_out', 'tglkadaluarsa'], 'safe'],
            [['penerimaandetail_id', 'terimamutasibarangdetail_id', 'returbarangdetail_id', 'mutasibarangdetail_id', 'pemusnahanbarangdetail_id', 'stokopnamebarangdetail_id', 'nostokopname', 'pemakaianbarangdetail_id', 'produksibarangdetail_id', 'storexpiredbarangdetail_id', 'satuanunit_nama', 'keterangan', 'tanggal_transaksi', 'ruangan_asal', 'ruangan_asal_nama', 'ruangan_tujuan', 'ruangan_tujuan_nama'], 'string'],
            [['qtystok_in', 'qtystok_out', 'stok'], 'number'],
            [['barang_nama', 'no_penerimaan'], 'string', 'max' => 100],
            [['ruangan_penerimaan', 'ruangan_asal_terimamutasi', 'ruangan_tujuan_terimamutasi', 'no_returpenerimaanbarang', 'ruangan_returpenerimaan', 'nomutasi_barang', 'ruangan_asal_mutasi', 'ruangan_tujuan_mutasi', 'ruangan_pemusnahan', 'ruangan_stokopname', 'ruangan_pemakaianbarang', 'ruangan_produksibarang', 'ruangan_storexpiredbarang', 'ruangan_nama'], 'string', 'max' => 50],
            [['noterimamutasi', 'no_pemakaianbarang', 'no_produksibarang', 'no_storexpired'], 'string', 'max' => 20],
            [['nopemusnahan'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokbarang_id' => 'Stokbarang ID',
            'tglstok_in' => 'Tglstok In',
            'tglstok_out' => 'Tglstok Out',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'penerimaandetail_id' => 'Penerimaandetail ID',
            'no_penerimaan' => 'No Penerimaan',
            'gudangpenerima_id' => 'Gudangpenerima ID',
            'ruangan_penerimaan' => 'Ruangan Penerimaan',
            'terimamutasibarangdetail_id' => 'Terimamutasibarangdetail ID',
            'noterimamutasi' => 'Noterimamutasi',
            'ruanganasal_id1' => 'Ruanganasal Id1',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'ruangan_asal_terimamutasi' => 'Ruangan Asal Terimamutasi',
            'ruangan_tujuan_terimamutasi' => 'Ruangan Tujuan Terimamutasi',
            'returbarangdetail_id' => 'Returbarangdetail ID',
            'no_returpenerimaanbarang' => 'No Returpenerimaanbarang',
            'ruanganretur_id' => 'Ruanganretur ID',
            'ruangan_returpenerimaan' => 'Ruangan Returpenerimaan',
            'mutasibarangdetail_id' => 'Mutasibarangdetail ID',
            'nomutasi_barang' => 'Nomutasi Barang',
            'ruanganasal_id2' => 'Ruanganasal Id2',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'ruangan_asal_mutasi' => 'Ruangan Asal Mutasi',
            'ruangan_tujuan_mutasi' => 'Ruangan Tujuan Mutasi',
            'pemusnahanbarangdetail_id' => 'Pemusnahanbarangdetail ID',
            'nopemusnahan' => 'Nopemusnahan',
            'ruangan_id3' => 'Ruangan Id3',
            'ruangan_pemusnahan' => 'Ruangan Pemusnahan',
            'stokopnamebarangdetail_id' => 'Stokopnamebarangdetail ID',
            'nostokopname' => 'Nostokopname',
            'ruangan_id4' => 'Ruangan Id4',
            'ruangan_stokopname' => 'Ruangan Stokopname',
            'pemakaianbarangdetail_id' => 'Pemakaianbarangdetail ID',
            'no_pemakaianbarang' => 'No Pemakaianbarang',
            'ruangan_id5' => 'Ruangan Id5',
            'ruangan_pemakaianbarang' => 'Ruangan Pemakaianbarang',
            'produksibarangdetail_id' => 'Produksibarangdetail ID',
            'no_produksibarang' => 'No Produksibarang',
            'ruangan_id6' => 'Ruangan Id6',
            'ruangan_produksibarang' => 'Ruangan Produksibarang',
            'storexpiredbarangdetail_id' => 'Storexpiredbarangdetail ID',
            'no_storexpired' => 'No Storexpired',
            'ruangan_id7' => 'Ruangan Id7',
            'ruangan_storexpiredbarang' => 'Ruangan Storexpiredbarang',
            'qtystok_in' => 'Qtystok In',
            'qtystok_out' => 'Qtystok Out',
            'stok' => 'Stok',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanunit_nama' => 'Satuanunit Nama',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'keterangan' => 'Keterangan',
            'tanggal_transaksi' => 'Tanggal Transaksi',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'ruangan_asal' => 'Ruangan Asal',
            'ruangan_asal_nama' => 'Ruangan Asal Nama',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'ruangan_tujuan_nama' => 'Ruangan Tujuan Nama',
        ];
    }
}
