<?php

namespace app\modules\rm\models;

use Yii;

/**
 * This is the model class for table "pesanbarang_t".
 *
 * @property int $pesanbarang_id
 * @property int $mutasibrg_id
 * @property int $pegawaipemesan_id
 * @property int $pegawaimengetahui_id
 * @property int $ruanganpemesan_id
 * @property string $no_pemesanan
 * @property string $tgl_pesanbarang
 * @property string $tgl_mintadikirim
 * @property string $keterangan_pesan
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
 *
 * @property MutasibarangT[] $mutasibarangTs
 * @property MutasibarangT $mutasibrg
 * @property PegawaiM $pegawaipemesan
 * @property PegawaiM $pegawaimengetahui
 * @property RuanganM $ruanganpemesan
 */
class PemesananBarangForm extends \yii\base\Model
{
    public $pesanbarang_id;
    public $mutasibrg_id;
    public $pegawaipemesan_id;
    public $pegawaimengetahui_id;
    public $ruanganpemesan_id;
    public $no_pemesanan;
    public $tgl_pesanbarang;
    public $tgl_mintadikirim;
    public $keterangan_pesan;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $barang_nama;
    public $qty_pesan;
    public $barang_m;
    public $pesanbarangdetail_t;
    public $idx_instalasi;
    public $idx_ruangan;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanbarang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibrg_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['mutasibrg_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pegawaipemesan_id', 'ruanganpemesan_id', 'no_pemesanan', 'tgl_pesanbarang','idx_instalasi','idx_ruangan'], 'required'],
            [['tgl_pesanbarang', 'tgl_mintadikirim', 'created_date', 'last_modified_date', 'deleted_date','barang_m','pesanbarangdetail_t'], 'safe'],
            [['keterangan_pesan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pemesanan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarang_id' => 'Pesanbarang ID',
            'mutasibrg_id' => 'Mutasibrg ID',
            'pegawaipemesan_id' => 'Pegawaipemesan ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'no_pemesanan' => 'No Pemesanan',
            'tgl_pesanbarang' => 'Tgl Pesanbarang',
            'tgl_mintadikirim' => 'Tgl Mintadikirim',
            'keterangan_pesan' => 'Keterangan Pesan',
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
            'idx_instalasi' => 'Instalasi Tujuan Pemesanan',
            'idx_ruangan' => 'Ruangan Tujuan Pemesanan',
            'barang_nama' => 'Barang',
            'tgl_mintadikirim' => 'Tanggal Kirim'
        ];
    }
}
