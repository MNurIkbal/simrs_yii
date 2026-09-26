<?php

namespace app\components\Traits\Form;

use Yii;

class SuratKeteranganPasienForm extends \yii\base\Model
{
    public $no_surat;
    public $surat_keterangan_pasien_id;
    public $pendaftaran_id;
    public $nama_pasien;
    public $tempat_lahir;
    public $tgl_lahir;
    public $jenis_kelamin;
    public $pekerjaan;
    public $alamat;
    public $atas_permintaan;
    public $pegawai_id;
    public $surat_keterangan_id;
    public $additional_data;
    public $nama_pegawai;
    public $nip_pegawai;
    public $jabatan_pegawai;
    public $umur;
    public $no_rekam_medik;
    public $dokter_pelaksana;
    public $pemberi_informasi;

    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $code_report;
    public $is_eklaim;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'surat_keterangan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'surat_keterangan_id', 'surat_keterangan_pasien_id'], 'integer'],
            [['surat_keterangan_id', 'pendaftaran_id', 'no_surat', 'nama_pasien', 'nama_pegawai', 'nip_pegawai', 'jabatan_pegawai','no_rekam_medik'], 'required'],
            [['nama_pasien', 'tempat_lahir', 'jenis_kelamin', 'pekerjaan', 'alamat', 'atas_permintaan','nama_pegawai', 'nip_pegawai', 'jabatan_pegawai','no_surat','no_rekam_medik','code_report'], 'string'],
            [['pegawai_id', 'tgl_lahir', 'additional_data', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active', 'is_eklaim'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => Yii::t('fe', 'ID Pendaftaran'),
            'dokter_pelaksana' => Yii::t('fe', 'Dokter Pelaksana Tindakan'),
            'pemberi_informasi' => Yii::t('fe', 'Pemberi Informasi'),
            'nama_pasien' => Yii::t('fe', 'Nama Pasien'),
            'no_rekam_medik' => Yii::t('fe', 'Nomor RM'),
            'tempat_lahir' => Yii::t('fe', 'Tempat Lahir'),
            'tgl_lahir' => Yii::t('fe', 'Tanggal Lahir'),
            'jenis_kelamin' => Yii::t('fe', 'Jenis Kelamin'),
            'alamat' => Yii::t('fe', 'Alamat'),
            'atas_permintaan' => Yii::t('fe', 'Atas Permintaan'),
            'nama_pegawai' => Yii::t('fe', 'Nama Pegawai'),
            'nip_pegawai' => Yii::t('fe', 'NIP Pegawai'),
            'jabatan_pegawai' => Yii::t('fe', 'Jabatan Pegawai'),
        ];
    }
}