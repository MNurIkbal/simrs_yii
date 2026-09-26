<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "informasipemakaianbarang_v".
 *
 * @property int $pemakaianbarangdetail_id
 * @property int $pemakaianbarang_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $pegawai_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $tgl_pemakaianbarang
 * @property string $no_pemakaianbarang
 * @property string $untuk_keperluan
 * @property string $keteranganpakai
 * @property string $created_date
 * @property string $last_modified_date
 * @property int $created_by
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property int $jumlah_pakai
 * @property string $satuan_pakai
 * @property int $barang_id
 * @property string $barang_nama
 */
class InformasiPemakaianBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informasipemakaianbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pemakaianbarangdetail_id', 'pemakaianbarang_id', 'ruangan_id', 'instalasi_id', 'pegawai_id', 'created_by', 'last_modified_by', 'jumlah_pakai', 'barang_id'], 'default', 'value' => null],
            [['pemakaianbarangdetail_id', 'pemakaianbarang_id', 'ruangan_id', 'instalasi_id', 'pegawai_id', 'created_by', 'last_modified_by', 'jumlah_pakai', 'barang_id'], 'integer'],
            [['tgl_pemakaianbarang', 'created_date', 'last_modified_date'], 'safe'],
            [['keteranganpakai'], 'string'],
            [['is_deleted'], 'boolean'],
            [['ruangan_nama', 'instalasi_nama', 'nama_pegawai', 'satuan_pakai'], 'string', 'max' => 50],
            [['gelardepan'], 'string', 'max' => 10],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['no_pemakaianbarang'], 'string', 'max' => 20],
            [['untuk_keperluan'], 'string', 'max' => 500],
            [['barang_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemakaianbarangdetail_id' => 'Pemakaianbarangdetail ID',
            'pemakaianbarang_id' => 'Pemakaianbarang ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'pegawai_id' => 'Pegawai ID',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'tgl_pemakaianbarang' => 'Tgl Pemakaianbarang',
            'no_pemakaianbarang' => 'No Pemakaianbarang',
            'untuk_keperluan' => 'Untuk Keperluan',
            'keteranganpakai' => 'Keteranganpakai',
            'created_date' => 'Created Date',
            'last_modified_date' => 'Last Modified Date',
            'created_by' => 'Created By',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'jumlah_pakai' => 'Jumlah Pakai',
            'satuan_pakai' => 'Satuan Pakai',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
        ];
    }
}
