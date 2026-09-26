<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanmutasibarangdetail_v".
 *
 * @property int $mutasibarangdetail_id
 * @property int $mutasibarang_id
 * @property string $nomutasi_barang
 * @property string $tgl_mutasibarang
 * @property int $ruanganasal_id
 * @property string $ruangan_asal
 * @property int $instalasiasal_id
 * @property string $instalasiasal_nama
 * @property int $ruangantujuan_id
 * @property string $ruangan_tujuan
 * @property int $instalasitujuan_id
 * @property string $instalasitujuan_nama
 * @property int $pegawaipengirim_id
 * @property string $pegawai_pengirim
 * @property int $pegawaimengetahui_id
 * @property string $pegawai_mengetahui
 * @property int $status_mutasi
 * @property string $status
 * @property int $barang_id
 * @property string $barang_nama
 * @property double $qty_mutasi
 * @property int $satuankecil_id
 * @property string $satuan_kecil
 * @property double $jumlah_input
 * @property int $satuanbesar_id
 * @property string $satuan_besar
 */
class LaporanMutasiBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanmutasibarangdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibarangdetail_id', 'mutasibarang_id', 'ruanganasal_id', 'instalasiasal_id', 'ruangantujuan_id', 'instalasitujuan_id', 'pegawaipengirim_id', 'pegawaimengetahui_id', 'status_mutasi', 'barang_id', 'satuankecil_id', 'satuanbesar_id'], 'default', 'value' => null],
            [['mutasibarangdetail_id', 'mutasibarang_id', 'ruanganasal_id', 'instalasiasal_id', 'ruangantujuan_id', 'instalasitujuan_id', 'pegawaipengirim_id', 'pegawaimengetahui_id', 'status_mutasi', 'barang_id', 'satuankecil_id', 'satuanbesar_id'], 'integer'],
            [['tgl_mutasibarang'], 'safe'],
            [['qty_mutasi', 'jumlah_input'], 'number'],
            [['satuan_kecil', 'satuan_besar'], 'string'],
            [['nomutasi_barang', 'ruangan_asal', 'instalasiasal_nama', 'ruangan_tujuan', 'instalasitujuan_nama', 'pegawai_pengirim', 'pegawai_mengetahui'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 200],
            [['barang_nama'], 'string', 'max' => 100],
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
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangan_asal' => 'Ruangan Asal',
            'instalasiasal_id' => 'Instalasiasal ID',
            'instalasiasal_nama' => 'Instalasiasal Nama',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'instalasitujuan_id' => 'Instalasitujuan ID',
            'instalasitujuan_nama' => 'Instalasitujuan Nama',
            'pegawaipengirim_id' => 'Pegawaipengirim ID',
            'pegawai_pengirim' => 'Pegawai Pengirim',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'pegawai_mengetahui' => 'Pegawai Mengetahui',
            'status_mutasi' => 'Status Mutasi',
            'status' => 'Status',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'qty_mutasi' => 'Qty Mutasi',
            'satuankecil_id' => 'Satuankecil ID',
            'satuan_kecil' => 'Satuan Kecil',
            'jumlah_input' => 'Jumlah Input',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuan_besar' => 'Satuan Besar',
        ];
    }
}
