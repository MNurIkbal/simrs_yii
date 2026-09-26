<?php
//author: Ardi Pratama

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "instruksitindakanbmhp_t".
 *
 * @property int $instruksitindakanbmhp_id
 * @property int $instruksi_id
 * @property int $instruksitindakan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $kelaspelayanan_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property string $tgl_pelayanan
 * @property int $daftartindakan_id
 * @property int $tipepaket_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $qty
 * @property double $harga_jualsatuan
 * @property double $harga_netto
 * @property double $harga_jumlah
 * @property bool $is_ditagihkan
 * @property int $dokter_id
 * @property int $perawat1_id
 * @property int $perawat2_id
 * @property string $status_implementasi
 * @property string $catatan
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
 */
class InstruksiTindakanBmhpForm extends \yii\base\Model
{
    public $obat;
    public $obatalkes_nama;
    public $instruksitindakanbmhp_id;
    public $instruksi_id;
    public $instruksitindakan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    public $penjamin_id;
    public $instalasi_id;
    public $ruangan_id;
    public $jeniskasuspenyakit_id;
    public $tgl_pelayanan;
    public $daftartindakan_id;
    public $tipepaket_id;
    public $obatalkes_id;
    public $satuankecil_id;
    public $qty;
    public $harga_jualsatuan;
    public $harga_netto;
    public $harga_jumlah;
    public $is_ditagihkan;
    public $dokter_id;
    public $perawat1_id;
    public $perawat2_id;
    public $status_implementasi;
    public $catatan;
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
    public $depo_id;
    /**
     * {@inheritdoc}
     */
    // public static function tableName()
    // {
    //     return 'instruksitindakanbmhp_t';
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['instruksitindakanbmhp_id', 'instruksi_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'tgl_pelayanan', 'obatalkes_id','obat', 'qty', 'depo_id'], 'required'],
            [['instruksitindakanbmhp_id', 'instruksi_id', 'instruksitindakan_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'daftartindakan_id', 'tipepaket_id', 'obatalkes_id', 'satuankecil_id', 'qty', 'dokter_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'depo_id'], 'default', 'value' => null],
            [['instruksitindakanbmhp_id', 'instruksi_id', 'instruksitindakan_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'daftartindakan_id', 'tipepaket_id', 'obatalkes_id', 'satuankecil_id', 'qty', 'dokter_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'depo_id'], 'integer'],
            [['tgl_pelayanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga_jualsatuan', 'harga_netto', 'harga_jumlah'], 'number'],
            [['is_ditagihkan', 'is_deleted', 'is_active'], 'boolean'],
            [['catatan', 'additional_data'], 'string'],
            [['status_implementasi'], 'string', 'max' => 100],
            [['instruksitindakanbmhp_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instruksitindakanbmhp_id' => 'Instruksitindakanbmhp ID',
            'instruksi_id' => 'Instruksi ID',
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'tgl_pelayanan' => 'Tanggal Pelayanan',
            'daftartindakan_id' => Yii::t('fe', 'Nama Tindakan'),
            'tipepaket_id' => 'Tipepaket ID',
            'obatalkes_id' => Yii::t('fe', 'Pemakaian Obat/Alkes'),
            'satuankecil_id' => 'Satuankecil ID',
            'qty' => 'Qty',
            'harga_jualsatuan' => 'Harga Jualsatuan',
            'harga_netto' => 'Harga Netto',
            'harga_jumlah' => Yii::t('fe', 'Jumlah Tarif'),
            'is_ditagihkan' => 'Is Ditagihkan',
            'dokter_id' => 'Dokter ID',
            'perawat1_id' => Yii::t('fe', 'Perawat 1'),
            'perawat2_id' => Yii::t('fe', 'Perawat 2'),
            'status_implementasi' => 'Status Implementasi',
            'catatan' => 'Catatan',
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
            'obat' => Yii::t('fe', 'Jenis Pemakaian'),
        ];
    }
}
