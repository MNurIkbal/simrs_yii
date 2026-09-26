<?php
namespace app\modules\pendaftaran\models;

use Yii;

class VerifikasiBantaranForm extends \yii\base\Model
{
    
    public $carabayar_id;
    public $penjamin_id;
    public $jadwaldokter_id;
    public $tgl_kunjungan;
    public $instalasi_id;
    public $ruangan_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {   
        return [
            [
                [
                    'carabayar_id', 
                    'penjamin_id', 
                    'jadwaldokter_id',
                    'tgl_kunjungan',
                    'instalasi_id',
                    'ruangan_id'
                ], 'required'
            ],
            //[['tgl_kunjungan', 'uptasal_nama', 'nama_pasien', 'created_date', 'last_modified_date', 'deleted_date','rujukan_id'], 'safe'],
            //[['is_deleted', 'is_active'], 'boolean'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'rujukanbantaran_id' => 'Rujukan Bantaran ID',
            'no_rujukanbantaran' => 'No Rujukan Bantaran',
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
            'created_by' => 'created by',
            'created_date' => 'created date',
            'last_modified_by' => 'last modified by',
            'last_modified_date' => 'last modified date',
            'deleted_by' => 'deleted by',
            'deleted_date' => 'deleted date',
            'is_deleted' => 'is deleted',
            'is_active' => 'is active',
        ];
    }
}