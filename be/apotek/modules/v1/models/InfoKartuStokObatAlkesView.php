<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infokartustokobatalkes_v".
 *
 * @property int $stokobatalkes_id
 * @property string $tglstok_in
 * @property string $tglstok_out
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property string $penerimaanobatdetail_id
 * @property string $no_penerimaan
 * @property int $gudangpenerima_id
 * @property string $ruangan_penerimaan
 * @property string $terimamutasidetail_id
 * @property string $noterimamutasi
 * @property int $ruanganasal_id1
 * @property int $ruanganpenerima_id
 * @property string $ruangan_asal_terimamutasi
 * @property string $ruangan_tujuan_terimamutasi
 * @property string $returresepdetail_id
 * @property string $no_returresep
 * @property int $ruangan_id1
 * @property string $ruangan_returresep
 * @property string $returpenerimaanobatdetail_id
 * @property string $no_returpenerimaanobat
 * @property int $ruanganretur_id
 * @property string $ruangan_returpenerimaan
 * @property string $mutasiobatdetail_id
 * @property string $nomutasioa
 * @property int $ruanganasal_id2
 * @property int $ruangantujuan_id
 * @property string $ruangan_asal_mutasi
 * @property string $ruangan_tujuan_mutasi
 * @property string $obatalkespasien_id
 * @property string $no_resep
 * @property int $ruangan_id2
 * @property string $ruangan_resep
 * @property string $pemusnahanobatdetail_id
 * @property string $nopemusnahan
 * @property int $ruangan_id3
 * @property string $ruangan_pemusnahan
 * @property string $stokopnamedetail_id
 * @property string $nostokopname
 * @property int $ruangan_id4
 * @property string $ruangan_stokopname
 * @property string $pemakaianobatdetail_id
 * @property string $nopemakaian_obat
 * @property int $ruangan_id5
 * @property string $ruangan_pemakaianobat
 * @property string $produksiobatdetail_id
 * @property string $no_produksiobat
 * @property int $ruangan_id6
 * @property string $ruangan_produksiobat
 * @property string $storexpiredobatdetail_id
 * @property string $no_storexpired
 * @property int $ruangan_id7
 * @property string $ruangan_storexpiredobat
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
class InfoKartuStokObatAlkesView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infokartustokobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stokobatalkes_id', 'obatalkes_id', 'gudangpenerima_id', 'ruanganasal_id1', 'ruanganpenerima_id', 'ruangan_id1', 'ruanganretur_id', 'ruanganasal_id2', 'ruangantujuan_id', 'ruangan_id2', 'ruangan_id3', 'ruangan_id4', 'ruangan_id5', 'ruangan_id6', 'ruangan_id7', 'satuankecil_id', 'ruangan_id'], 'default', 'value' => null],
            [['stokobatalkes_id', 'obatalkes_id', 'gudangpenerima_id', 'ruanganasal_id1', 'ruanganpenerima_id', 'ruangan_id1', 'ruanganretur_id', 'ruanganasal_id2', 'ruangantujuan_id', 'ruangan_id2', 'ruangan_id3', 'ruangan_id4', 'ruangan_id5', 'ruangan_id6', 'ruangan_id7', 'satuankecil_id', 'ruangan_id'], 'integer'],
            [['tglstok_in', 'tglstok_out', 'tglkadaluarsa'], 'safe'],
            [['penerimaanobatdetail_id', 'terimamutasidetail_id', 'returresepdetail_id', 'no_returresep', 'returpenerimaanobatdetail_id', 'mutasiobatdetail_id', 'nomutasioa', 'obatalkespasien_id', 'no_resep', 'pemusnahanobatdetail_id', 'stokopnamedetail_id', 'nostokopname', 'pemakaianobatdetail_id', 'produksiobatdetail_id', 'storexpiredobatdetail_id', 'satuanunit_nama', 'keterangan', 'tanggal_transaksi', 'ruangan_asal', 'ruangan_asal_nama', 'ruangan_tujuan', 'ruangan_tujuan_nama'], 'string'],
            [['qtystok_in', 'qtystok_out', 'stok'], 'number'],
            [['obatalkes_nama'], 'string', 'max' => 255],
            [['no_penerimaan'], 'string', 'max' => 100],
            [['ruangan_penerimaan', 'ruangan_asal_terimamutasi', 'ruangan_tujuan_terimamutasi', 'ruangan_returresep', 'no_returpenerimaanobat', 'ruangan_returpenerimaan', 'ruangan_asal_mutasi', 'ruangan_tujuan_mutasi', 'ruangan_resep', 'ruangan_pemusnahan', 'ruangan_stokopname', 'ruangan_pemakaianobat', 'ruangan_produksiobat', 'ruangan_storexpiredobat', 'ruangan_nama'], 'string', 'max' => 50],
            [['noterimamutasi', 'nopemakaian_obat', 'no_produksiobat', 'no_storexpired'], 'string', 'max' => 20],
            [['nopemusnahan'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokobatalkes_id' => 'Stokobatalkes ID',
            'tglstok_in' => 'Tglstok In',
            'tglstok_out' => 'Tglstok Out',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'penerimaanobatdetail_id' => 'Penerimaanobatdetail ID',
            'no_penerimaan' => 'No Penerimaan',
            'gudangpenerima_id' => 'Gudangpenerima ID',
            'ruangan_penerimaan' => 'Ruangan Penerimaan',
            'terimamutasidetail_id' => 'Terimamutasidetail ID',
            'noterimamutasi' => 'Noterimamutasi',
            'ruanganasal_id1' => 'Ruanganasal Id1',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'ruangan_asal_terimamutasi' => 'Ruangan Asal Terimamutasi',
            'ruangan_tujuan_terimamutasi' => 'Ruangan Tujuan Terimamutasi',
            'returresepdetail_id' => 'Returresepdetail ID',
            'no_returresep' => 'No Returresep',
            'ruangan_id1' => 'Ruangan Id1',
            'ruangan_returresep' => 'Ruangan Returresep',
            'returpenerimaanobatdetail_id' => 'Returpenerimaanobatdetail ID',
            'no_returpenerimaanobat' => 'No Returpenerimaanobat',
            'ruanganretur_id' => 'Ruanganretur ID',
            'ruangan_returpenerimaan' => 'Ruangan Returpenerimaan',
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'nomutasioa' => 'Nomutasioa',
            'ruanganasal_id2' => 'Ruanganasal Id2',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'ruangan_asal_mutasi' => 'Ruangan Asal Mutasi',
            'ruangan_tujuan_mutasi' => 'Ruangan Tujuan Mutasi',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'no_resep' => 'No Resep',
            'ruangan_id2' => 'Ruangan Id2',
            'ruangan_resep' => 'Ruangan Resep',
            'pemusnahanobatdetail_id' => 'Pemusnahanobatdetail ID',
            'nopemusnahan' => 'Nopemusnahan',
            'ruangan_id3' => 'Ruangan Id3',
            'ruangan_pemusnahan' => 'Ruangan Pemusnahan',
            'stokopnamedetail_id' => 'Stokopnamedetail ID',
            'nostokopname' => 'Nostokopname',
            'ruangan_id4' => 'Ruangan Id4',
            'ruangan_stokopname' => 'Ruangan Stokopname',
            'pemakaianobatdetail_id' => 'Pemakaianobatdetail ID',
            'nopemakaian_obat' => 'Nopemakaian Obat',
            'ruangan_id5' => 'Ruangan Id5',
            'ruangan_pemakaianobat' => 'Ruangan Pemakaianobat',
            'produksiobatdetail_id' => 'Produksiobatdetail ID',
            'no_produksiobat' => 'No Produksiobat',
            'ruangan_id6' => 'Ruangan Id6',
            'ruangan_produksiobat' => 'Ruangan Produksiobat',
            'storexpiredobatdetail_id' => 'Storexpiredobatdetail ID',
            'no_storexpired' => 'No Storexpired',
            'ruangan_id7' => 'Ruangan Id7',
            'ruangan_storexpiredobat' => 'Ruangan Storexpiredobat',
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
