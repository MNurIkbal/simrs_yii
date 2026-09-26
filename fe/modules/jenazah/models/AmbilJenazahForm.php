<?php

namespace app\modules\jenazah\models;

use Yii;

/**
 * This is the model class for table "ambiljenazah_t".
 *
 * @property int $ambiljenazah_id
 * @property int $pasien_id
 * @property int $ruangan_id ruangan meninggal
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $tgl_pengambilan
 * @property string $tgl_meninggal
 * @property string $kondisi
 * @property int $hubungan_keluarga lookup_type='hubungan_keluarga'
 * @property string $nama_pengambil
 * @property int $jenis_identitas lookup_type='jenis_identitas'
 * @property string $no_identitas
 * @property string $tempat_lahir
 * @property string $tgl_lahir
 * @property string $pekerjaan
 * @property string $alamat
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
class AmbilJenazahForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $ambiljenazah_id;
    public $pasien_id;
    public $ruangan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $tgl_pengambilan;
    public $tgl_meninggal;
    public $kondisi;
    public $hubungan_keluarga;
    public $nama_pengambil;
    public $jenis_identitas;
    public $no_identitas;
    public $tempat_lahir;
    public $tgl_lahir;
    public $pekerjaan;
    public $alamat;
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

    public $umur;
    public $instalasi_asal;
    public $nama_pasien;
    public $jenis_kelamin;
    public $alamat_pasien;
    public $ruangan_nama;
    public $pegawai_ruangan;
    public $jabatan_nama;

    public static function tableName()
    {
        return 'ambiljenazah_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kondisi', 'jenis_identitas', 'no_identitas', 'tempat_lahir', 'tgl_lahir', 'alamat', 'pasien_id', 'ruangan_id', 'pendaftaran_id', 'tgl_meninggal', 'hubungan_keluarga', 'nama_pengambil'], 'required'],
            
            [['pasien_id', 'ruangan_id', 'pendaftaran_id', 'pasienadmisi_id', 'hubungan_keluarga', 'jenis_identitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['umur', 'instalasi_asal', 'nama_pasien', 'jenis_kelamin', 'alamat_pasien', 'ruangan_nama', 'pasien_id', 'ruangan_id', 'pendaftaran_id', 'pasienadmisi_id', 'hubungan_keluarga', 'jenis_identitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pegawai_ruangan', 'jabatan_nama', 'tgl_pengambilan', 'tgl_meninggal', 'tgl_lahir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kondisi', 'alamat', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_pengambil', 'tempat_lahir'], 'string', 'max' => 100],
            [['no_identitas', 'pekerjaan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambiljenazah_id' => 'Ambiljenazah ID',
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_pengambilan' => 'Tgl Pengambilan',
            'tgl_meninggal' => 'Tgl Meninggal',
            'kondisi' => 'Kondisi',
            'hubungan_keluarga' => 'Hubungan Keluarga',
            'nama_pengambil' => 'Nama Pengambil',
            'jenis_identitas' => 'Jenis Identitas',
            'no_identitas' => 'No Identitas',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tgl Lahir',
            'pekerjaan' => 'Pekerjaan',
            'alamat' => 'Alamat',
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
