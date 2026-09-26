<?php
//author: Ardi Pratama

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "instruksitindakan_t".
 *
 * @property int $instruksitindakan_id
 * @property int $instruksi_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $kelaspelayanan_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property string $tgl_tindakan
 * @property int $daftartindakan_id
 * @property int $tipepaket_id
 * @property int $qty
 * @property bool $is_cyto
 * @property double $tarif_satuan
 * @property double $tarif_cyto
 * @property double $jumlah_tarif
 * @property int $dokterdpjp_id
 * @property int $dokterdelegasi_id
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
class InstruksiTindakanForm extends \yii\base\Model
{
	public $instruksitindakan_id;
    public $instruksi_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    public $penjamin_id;
    public $instalasi_id;
    public $ruangan_id;
    public $jeniskasuspenyakit_id;
    public $tgl_tindakan;
    public $daftartindakan_id;
    public $tipepaket_id;
    public $qty;
    public $is_cyto;
    public $tarif_satuan;
    public $tarif_cyto;
    public $tarif_tindakan;
    public $jumlah_tarif;
    public $dokterdpjp_id;
    public $dokterdelegasi_id;
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
	
    /**
     * {@inheritdoc}
     */
    // public static function tableName()
    // {
    //     return 'instruksitindakan_t';
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['instruksi_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'tgl_tindakan', 'qty', 'dokterdpjp_id', 'daftartindakan_id'], 'required'],
            [['instruksitindakan_id', 'instruksi_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'daftartindakan_id', 'tipepaket_id', 'qty', 'dokterdpjp_id', 'dokterdelegasi_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['instruksitindakan_id', 'instruksi_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'daftartindakan_id', 'tipepaket_id', 'qty', 'dokterdpjp_id', 'dokterdelegasi_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_tindakan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_cyto', 'is_deleted', 'is_active'], 'boolean'],
            [['tarif_satuan', 'tarif_cyto', 'jumlah_tarif'], 'number'],
            [['catatan', 'additional_data'], 'string'],
            [['status_implementasi'], 'string', 'max' => 100],
            [['instruksitindakan_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'instruksi_id' => 'Instruksi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'tgl_tindakan' => 'Tanggal Tindakan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'tipepaket_id' => 'Tipepaket ID',
            'qty' => 'Jumlah Tindakan',
            'is_cyto' => 'Is Cyto',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_cyto' => 'Tarif Cyto',
            'tarif_tindakan' => 'Jumlah Tarif',
            'jumlah_tarif' => 'Jumlah Tarif',
            'dokterdpjp_id' => 'Dokter Pemeriksa',
            'dokterdelegasi_id' => 'Dokterdelegasi ID',
            'perawat1_id' => 'Perawat 1',
            'perawat2_id' => 'Perawat 2',
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
        ];
    }
}
