<?php

namespace app\modules\v1\models;

class RujukanBantaran extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rujukanbantaran_t';
    }

    public function rules()
    {   
        return [
            [
                [
                    'uptasal_id', 
                    'uptasal_nama', 
                    'nama_pasien',
                    'no_identitas_pasien',
                    'tempat_lahir',
                    'tgl_lahir',
                    'jenis_kelamin',
                    'pengajuan_id',
                    'keterangan_rujukan',
                    'no_rujukanbantaran',
                    'pasien_lama',
                    'no_telp',
                    'no_tahanan',
                    'no_telepon',
                ], 'required'
            ],
            [['uptasal_id', 'pengajuan_id', 'created_by', 'last_modified_by', 'deleted_by'], 'integer'],
            [
                [
                    'no_rekam_medik',
                    'tgl_kunjungan', 
                    'uptasal_nama', 
                    'nama_pasien', 
                    'created_date', 
                    'last_modified_date', 
                    'deleted_date',
                    'rujukan_id',
                    'no_rujukanbantaran',
                    'instalasi_id',
                    'no_tahanan',
                    'no_telepon',
                    'tujuan_pemeriksaan',
                    'tahanan_url'
                ], 
                'safe'
            ],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'rujukanbantaran_id' => 'Rujukan Bantaran ID',
            'no_rujukanbantaran' => 'No Rujukan Bantaran',
            'no_tahanan' => 'No Tahanan',
            'no_telepon' => 'No Telepon',
            'tgl_kunjungan' => 'Tgl Kunjungan',
            'uptasal_id' => 'UPT asal ID',
            'uptasal_nama' => 'UPT asal nama',
            'instalasi_id' => 'instalasi ID',
            'ruangan_id' => 'ruangan ID',
            'dokter_id' => 'dokter ID',
            'jadwaldokter_id' => 'jadwaldokter ID',
            'jam_buka' => 'jam buka',
            'jam_tutup' => 'jam tutup',
            'carabayar_id' => 'cara bayar id',
            'penjamin_id' => 'penjamin id',
            'pasien_id' => 'pasien id',
            'no_rekam_medik' => 'no rekam medik',
            'nama_pasien' => 'nama pasien',
            'jenis_identitas' => 'jenis identitas pasien',
            'no_identitas_pasien' => 'no identitas pasien',
            'tempat_lahir' => 'tempat lahir',
            'tgl_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'keterangan_rujukan' => 'keterangan rujukan',
            'status_unduh_dokumen' => 'status unduh dokumen',
            'keterangan_penolakan_rujukan' => 'keterangan penolakan rujukan',
            'status_verifikasi_bantaran' => 'status verifikasi bantaran',
            'pegawaiverifikasi_id' => 'pegawai verifikasi id',
            'tgl_verifikasi_bantaran' => 'tgl verifikasi bantaran',
            'status_pelayanan_bantaran' => 'status pelayanan bantaran',
            'pendaftaran_id' => 'pendaftaran id',
            'tujuan_pemeriksaan' => 'tujuan pemeriksaan',
            'created_by' => 'created by',
            'created_date' => 'created date',
            'last_modified_by' => 'last modified by',
            'last_modified_date' => 'last modified date',
            'deleted_by' => 'deleted by',
            'deleted_date' => 'deleted date',
            'is_deleted' => 'is deleted',
            'is_active' => 'is active',
            'pengajuan_id' => 'Pengajuan ID',
            'pasien_lama' => 'Pasien Lama',
            'no_telp' => 'No Telp Pasien',
            'tahanan_url' => 'Tahanan URL',
        ];
    }
}