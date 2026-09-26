<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "pesandarah_t".
 *
 * @property int $pesandarah_id
 * @property string $no_pesandarah
 * @property string $tgl_pesandarah
 * @property int $ruanganpemesan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $golongandarah_id
 * @property string $indikasi_transfusi
 * @property int $metode_pengambilan lookup_type='pengambilan_darah'
 * @property double $total_harga
 * @property int $total_kantongdarah
 * @property string $riwayat_transfusi
 * @property string $riwayat_kehamilan
 * @property string $keterangan
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
class PesanDarahForm extends \yii\base\Model
{
     public $pesandarah_id;
     public $no_pesandarah;
     public $tgl_pesandarah;
     public $ruanganpemesan_id;
     public $pendaftaran_id;
     public $pasienadmisi_id;
     public $pasien_id;
     public $golongandarah_id;
     public $indikasi_transfusi;
     public $metode_pengambilan;
     public $total_harga;
     public $total_kantongdarah;
     public $riwayat_transfusi;
     public $riwayat_kehamilan;
     public $keterangan;
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
     public $alamat;
     public $no_tlp;
     public $diagnosa_sementara;
     public $dokter_id;
     public $no_rekam_medik;
     public $umur;
     public $kadar_hb;
     public $jenis_kelamin;
     public $golongandarah_nama;
     
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pesandarah_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dokter_id', 'pasien_id', 'metode_pengambilan'], 'required'],
            [['dokter_id', 'tgl_pesandarah', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['ruanganpemesan_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'golongandarah_id', 'metode_pengambilan', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruanganpemesan_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'golongandarah_id', 'metode_pengambilan', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['total_harga'], 'number'],
            [['riwayat_transfusi', 'riwayat_kehamilan', 'keterangan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pesandarah', 'indikasi_transfusi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesandarah_id' => 'Pesandarah ID',
            'no_pesandarah' => 'No Pesandarah',
            'tgl_pesandarah' => 'Tgl Pesandarah',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Nama Pasien',
            'golongandarah_id' => 'Golongandarah ID',
            'indikasi_transfusi' => 'Indikasi Transfusi',
            'metode_pengambilan' => 'Metode Pengambilan',
            'total_harga' => 'Total Harga',
            'total_kantongdarah' => 'Total Kantongdarah',
            'riwayat_transfusi' => 'Riwayat Transfusi',
            'riwayat_kehamilan' => 'Riwayat Kehamilan',
            'keterangan' => 'Keterangan',
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
            'dokter_id' => 'Dokter'
        ];
    }
}
