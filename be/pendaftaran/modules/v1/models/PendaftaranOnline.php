<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-25 14:13:59
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pendaftaranol_t".
 *
 * @property int $pendaftaranol_id
 * @property int $pendaftaran_id
 * @property string $no_pendaftaranol
 * @property string $tgl_pendaftaranol
 * @property string $jam_kunjungan
 * @property int $pasien_id
 * @property int $carabayar_id carabayar_m
 * @property int $penjamin_id
 * @property int $ruangan_id ruangan_m where instalasi_id=1
 * @property int $pegawai_id pegawai_v kelompokpegawai_id=1
 * @property int $shift_id
 * @property string $no_asuransi
 * @property string $no_rujukan
 * @property int $status_pasien lookup_type='status_pasien'
 * @property int $status_daftar_ol lookup_type='status_daftar_ol'
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
 * @property string $alamat_pasien
 * @property int $referral_doctor_id
 * @property string $email
 * @property string $note
 * @property string $reference_letter
 */
class PendaftaranOnline extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pendaftaranol_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'pegawai_id', 'shift_id', 'status_pasien', 'status_daftar_ol', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jadwaldokter_id', 'jadwalbukapoli_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'pegawai_id', 'shift_id', 'status_pasien', 'status_daftar_ol', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jadwaldokter_id', 'jadwalbukapoli_id'], 'integer'],
            [['tgl_pendaftaranol', 'created_date', 'last_modified_date', 'deleted_date', 'no_telepon_pasien', 'alamat_pasien', 'referral_doctor_id', 'email', 'note', 'reference_letter', 'is_cetaktracer'], 'safe'],
            [['ruangan_id'], 'required'],
            [['additional_data', 'jam_mulai', 'jam_tutup'], 'string'],
            [['is_deleted', 'is_active', 'is_cetaktracer'], 'boolean'],
            [['no_pendaftaranol', 'jam_kunjungan'], 'string', 'max' => 100],
            [['no_asuransi', 'no_rujukan', 'no_bpjs'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaranol_id' => 'Pendaftaran Online ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaranol' => 'No Pendaftaran Online',
            'tgl_pendaftaranol' => 'Tanggal Pendaftaran Online',
            'jam_kunjungan' => 'Jam Kunjungan',
            'pasien_id' => 'Pasien ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'shift_id' => 'Shift ID',
            'no_asuransi' => 'No Asuransi',
            'no_rujukan' => 'No Rujukan',
            'status_pasien' => 'Status Pasien',
            'status_daftar_ol' => 'Status Daftar Online',
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
            'jadwaldokter_id' => 'Jadwal Dokter ID',
            'jam_mulai' => 'Jam Mulai',
            'jam_tutup' => 'Jam Tutup',
            'no_bpjs' => 'No BPJS',
            'alamat_pasien' => 'Alamat Pasien',
            'referral_doctor_id' => 'Referral Dokter',
            'email' => 'Email',
            'note' => 'Catatan',
            'reference_letter' => 'Surat Rujukan',
        ];
    }
}
