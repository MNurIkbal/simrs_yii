<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_m".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property int $golonganumur_id
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property int $kecamatan_id
 * @property int $kelurahan_id
 * @property int $pendidikan_id
 * @property int $pekerjaan_id
 * @property int $suku_id
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property int $anakke
 * @property int $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $nama_ibu
 * @property string $nama_ayah
 * @property int $dokrekammedis_id
 * @property string $tgl_meninggal
 * @property int $pegawai_id
 * @property int $loginpemakai_id
 * @property double $garis_latitude
 * @property double $garis_longitude
 * @property string $statusrekammedis
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
 * @property int $profilrs_id
 * @property string $alamat_sekarang
 * @property string $nopeserta_bpjs
 * @property bool $is_aps
 *
 */
class Pasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasien_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_rekam_medik', 'namadepan', 'nama_pasien', 'tanggal_lahir', 'golonganumur_id', 'alamat_pasien', 'nama_ibu', 'statusrekammedis'], 'required'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'default', 'value' => null],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data', 'alamat_sekarang'], 'string'],
            [['garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active', 'is_aps'], 'boolean'],
            [['no_rekam_medik', 'statusrekammedis'], 'string', 'max' => 10],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin'], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            [['nopeserta_bpjs'], 'string', 'max' => 255],
            [['nama_ibu', 'tanggal_lahir', 'nama_pasien'], 'unique', 'targetAttribute' => ['nama_ibu', 'tanggal_lahir', 'nama_pasien']],
            [['no_rekam_medik'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'jenisidentitas' => 'Jenisidentitas',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'golonganumur_id' => 'Golonganumur ID',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'propinsi_id' => 'Propinsi ID',
            'kabupaten_id' => 'Kabupaten ID',
            'kecamatan_id' => 'Kecamatan ID',
            'kelurahan_id' => 'Kelurahan ID',
            'pendidikan_id' => 'Pendidikan ID',
            'pekerjaan_id' => 'Pekerjaan ID',
            'suku_id' => 'Suku ID',
            'statusperkawinan' => 'Statusperkawinan',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'warga_negara' => 'Warga Negara',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'nama_ibu' => 'Nama Ibu',
            'nama_ayah' => 'Nama Ayah',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'tgl_meninggal' => 'Tgl Meninggal',
            'pegawai_id' => 'Pegawai ID',
            'loginpemakai_id' => 'Loginpemakai ID',
            'garis_latitude' => 'Garis Latitude',
            'garis_longitude' => 'Garis Longitude',
            'statusrekammedis' => 'Statusrekammedis',
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
            'profilrs_id' => 'Profilrs ID',
            'alamat_sekarang' => 'Alamat Sekarang',
            'nopeserta_bpjs' => 'Nopeserta Bpjs',
            'is_aps' => 'Is Aps',
        ];
    }

}
