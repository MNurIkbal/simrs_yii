<?php

namespace Doco\models;

class SuratKeteranganPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'surat_keterangan_pasien_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surat_keterangan_id', 'pendaftaran_id', 'no_surat', 'nama_pasien', 'nama_pegawai', 'nip_pegawai', 'jabatan_pegawai','no_rekam_medik'], 'required'],
            [['surat_keterangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['no_surat', 'nama_pasien', 'tempat_lahir', 'jenis_kelamin', 'pekerjaan', 'alamat', 'atas_permintaan', 'jabatan_pegawai','no_rekam_medik'], 'string'],
            [['tgl_lahir', 'additional_data', 'created_date', 'last_modified_date', 'deleted_date','no_rekam_medik'], 'safe'],
            [['is_deleted', 'is_active', 'is_eklaim'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'surat_keterangan_id' => 'Surat Keterangan ID',
            'no_surat' => 'No Surat',
            'nama_pasien' => 'Nama Pasien',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'pekerjaan' => 'Pekerjaan',
            'alamat' => 'Alamat',
            'atas_permintaan' => 'Atas Permintaan',
            'nama_pegawai' => 'Nama Pegawai',
            'nip_pegawai' => 'NIP Pegawai',
            'jabatan_pegawai' => 'Jabatan Pegawai',
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
            'dokter_pelaksana' => "Dokter Pelaksana Tindakan",
            'pemberi_informasi' => "Pemberi Informasi",
            'is_eklaim' => 'Is Eklaim',
        ];
    }
}
