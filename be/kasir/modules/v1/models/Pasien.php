<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_m".
 *
 * @property integer $pasien_id
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
 * @property integer $kelompokumur_id
 * @property string $alamat_pasien
 * @property integer $rt
 * @property integer $rw
 * @property integer $propinsi_id
 * @property integer $kabupaten_id
 * @property integer $kecamatan_id
 * @property integer $kelurahan_id
 * @property integer $pendidikan_id
 * @property integer $pekerjaan_id
 * @property integer $suku_id
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property integer $anakke
 * @property integer $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $nama_ibu
 * @property string $nama_ayah
 * @property integer $dokrekammedis_id
 * @property string $tgl_meninggal
 * @property integer $pegawai_id
 * @property integer $loginpemakai_id
 * @property double $garis_latitude
 * @property double $garis_longitude
 * @property string $statusrekammedis
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $profilrs_id
 */
class Pasien extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasien_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_rekam_medik', 'tgl_rekam_medik', 'nama_pasien', 'jeniskelamin', 'tanggal_lahir', 'kelompokumur_id', 'alamat_pasien'], 'required'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kelompokumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data'], 'string'],
            [['garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_rekam_medik', 'statusrekammedis'], 'string', 'max' => 10],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin'], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            // [['dokrekammedis_id'], 'exist', 'skipOnError' => true, 'targetClass' => DokrekammedisM::className(), 'targetAttribute' => ['dokrekammedis_id' => 'dokrekammedis_id']],
            // [['kabupaten_id'], 'exist', 'skipOnError' => true, 'targetClass' => KabupatenM::className(), 'targetAttribute' => ['kabupaten_id' => 'kabupaten_id']],
            // [['kecamatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KecamatanM::className(), 'targetAttribute' => ['kecamatan_id' => 'kecamatan_id']],
            // [['kelompokumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokumurM::className(), 'targetAttribute' => ['kelompokumur_id' => 'kelompokumur_id']],
            // [['kelurahan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelurahanM::className(), 'targetAttribute' => ['kelurahan_id' => 'kelurahan_id']],
            // [['pekerjaan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PekerjaanM::className(), 'targetAttribute' => ['pekerjaan_id' => 'pekerjaan_id']],
            // [['pendidikan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendidikanM::className(), 'targetAttribute' => ['pendidikan_id' => 'pendidikan_id']],
            // [['propinsi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropinsiM::className(), 'targetAttribute' => ['propinsi_id' => 'propinsi_id']],
            // [['suku_id'], 'exist', 'skipOnError' => true, 'targetClass' => SukuM::className(), 'targetAttribute' => ['suku_id' => 'suku_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => Yii::t('app', 'Pasien ID'),
            'no_rekam_medik' => Yii::t('app', 'No Rekam Medik'),
            'tgl_rekam_medik' => Yii::t('app', 'Tgl Rekam Medik'),
            'jenisidentitas' => Yii::t('app', 'Jenisidentitas'),
            'no_identitas_pasien' => Yii::t('app', 'No Identitas Pasien'),
            'namadepan' => Yii::t('app', 'Namadepan'),
            'nama_pasien' => Yii::t('app', 'Nama Pasien'),
            'nama_bin' => Yii::t('app', 'Nama Bin'),
            'jeniskelamin' => Yii::t('app', 'Jeniskelamin'),
            'tempat_lahir' => Yii::t('app', 'Tempat Lahir'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal Lahir'),
            'kelompokumur_id' => Yii::t('app', 'Kelompokumur ID'),
            'alamat_pasien' => Yii::t('app', 'Alamat Pasien'),
            'rt' => Yii::t('app', 'Rt'),
            'rw' => Yii::t('app', 'Rw'),
            'propinsi_id' => Yii::t('app', 'Propinsi ID'),
            'kabupaten_id' => Yii::t('app', 'Kabupaten ID'),
            'kecamatan_id' => Yii::t('app', 'Kecamatan ID'),
            'kelurahan_id' => Yii::t('app', 'Kelurahan ID'),
            'pendidikan_id' => Yii::t('app', 'Pendidikan ID'),
            'pekerjaan_id' => Yii::t('app', 'Pekerjaan ID'),
            'suku_id' => Yii::t('app', 'Suku ID'),
            'statusperkawinan' => Yii::t('app', 'Statusperkawinan'),
            'agama' => Yii::t('app', 'Agama'),
            'golongandarah' => Yii::t('app', 'Golongandarah'),
            'rhesus' => Yii::t('app', 'Rhesus'),
            'anakke' => Yii::t('app', 'Anakke'),
            'jumlah_bersaudara' => Yii::t('app', 'Jumlah Bersaudara'),
            'no_telepon_pasien' => Yii::t('app', 'No Telepon Pasien'),
            'no_mobile_pasien' => Yii::t('app', 'No Mobile Pasien'),
            'warga_negara' => Yii::t('app', 'Warga Negara'),
            'photopasien' => Yii::t('app', 'Photopasien'),
            'alamatemail' => Yii::t('app', 'Alamatemail'),
            'nama_ibu' => Yii::t('app', 'Nama Ibu'),
            'nama_ayah' => Yii::t('app', 'Nama Ayah'),
            'dokrekammedis_id' => Yii::t('app', 'Dokrekammedis ID'),
            'tgl_meninggal' => Yii::t('app', 'Tgl Meninggal'),
            'pegawai_id' => Yii::t('app', 'Pegawai ID'),
            'loginpemakai_id' => Yii::t('app', 'Loginpemakai ID'),
            'garis_latitude' => Yii::t('app', 'Garis Latitude'),
            'garis_longitude' => Yii::t('app', 'Garis Longitude'),
            'statusrekammedis' => Yii::t('app', 'Statusrekammedis'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'profilrs_id' => Yii::t('app', 'Profilrs ID'),
        ];
    }
}
