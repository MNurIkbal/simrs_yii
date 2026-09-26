<?php

namespace app\modules\jenazah\models;

use Yii;

/**
 * This is the model class for table "pasienmasukpenunjang_t".
 *
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property int $kelaspelayanan_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pasienadmisi_id
 * @property int $pegawai_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $ruanganasal_id
 * @property string $no_masukpenunjang
 * @property string $tglmasukpenunjang
 * @property string $no_antrian
 * @property string $kunjungan lookup_type='kunjungan'
 * @property string $status_periksa lookup_type='status_periksa_penunjang'
 * @property bool $panggil_antrian
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
 * @property int $instalasiasal_id
 * @property bool $is_bayar
 * @property int $implementasi_id
 * @property string $catatan
 * @property string $tanggal_verifikasi
 * @property bool $is_alatlepas
 */

 

class PasienMasukPenunjangForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
     public $pasienmasukpenunjang_id;
     public $pasienkirimkeunitlain_id;
     public $kelaspelayanan_id;
     public $jeniskasuspenyakit_id;
     public $pasienadmisi_id;
     public $pegawai_id;
     public $ruangan_id;
     public $pasien_id;
     public $pendaftaran_id;
     public $ruanganasal_id;
     public $no_masukpenunjang;
     public $tglmasukpenunjang;
     public $no_antrian;
     public $kunjungan;
     public $status_periksa;
     public $panggil_antrian;
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
     public $instalasiasal_id;
     public $is_bayar;
     public $implementasi_id;
     public $catatan;
     public $tanggal_verifikasi;
     public $is_alatlepas;
     public $nama_pasien;
     public $pegawai_ruangan;
     public $umur;
     public $jabatan_nama;
     public $alamat_pasien;
     public $jabatan_pegjenazah;
     public $tglserah_terima;
     public $instalasi_asal;
     public $jenis_kelamin;
     public $ruangan_nama;
     public $tgl_meninggal;
     public $catatan_proses;
     
    public static function tableName()
    {
        return 'pasienmasukpenunjang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienkirimkeunitlain_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pasienadmisi_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instalasiasal_id', 'implementasi_id'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id', 'pasienadmisi_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instalasiasal_id', 'implementasi_id'], 'integer'],
            [[/*'ruangan_id', */'tglserah_terima', 'pasien_id', 'tglmasukpenunjang', 'pegawai_id'], 'required'],
            [['jenis_kelamin', 'ruangan_nama', 'tgl_meninggal', 'jabatan_pegjenazah', 'nama_pasien', 'pegawai_ruangan', 'umur', 'jabatan_nama', 'alamat_pasien', 'tglmasukpenunjang', 'created_date', 'last_modified_date', 'deleted_date', 'tanggal_verifikasi'], 'safe'],
            [['panggil_antrian', 'is_deleted', 'is_active', 'is_bayar', 'is_alatlepas'], 'boolean'],
            [['additional_data', 'catatan'], 'string'],
            [['no_masukpenunjang'], 'string', 'max' => 20],
            [['no_antrian'], 'string', 'max' => 100],
            [['kunjungan', 'status_periksa'], 'string', 'max' => 50],
            [['no_masukpenunjang'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Petugas Jenazah',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'ruanganasal_id' => 'Ruanganasal ID',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_antrian' => 'No Antrian',
            'kunjungan' => 'Kunjungan',
            'status_periksa' => 'Status Periksa',
            'panggil_antrian' => 'Panggil Antrian',
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
            'instalasiasal_id' => 'Instalasiasal ID',
            'is_bayar' => 'Is Bayar',
            'implementasi_id' => 'Implementasi ID',
            'catatan' => 'Catatan',
            'tanggal_verifikasi' => 'Tanggal Verifikasi',
            'is_alatlepas' => 'Is Alatlepas',
        ];
    }
}
