<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienadmisi_t".
 *
 * @property int $pasienadmisi_id
 * @property int $shift_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $caramasuk_id
 * @property int $ruangan_id
 * @property int $pasienpulang_id
 * @property int $bookingkamar_id
 * @property int $pembayaranpelayanan_id
 * @property int $pendaftaran_id
 * @property int $kamarruangan_id
 * @property int $kelaspelayanan_id
 * @property int $pegawai_id
 * @property string $tgl_admisi
 * @property string $tgl_pendaftaran
 * @property string $tgl_pulang
 * @property string $kunjungan
 * @property bool $status_keluar
 * @property bool $rawat_gabung
 * @property string $rencana_pulang
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
 * @property int $kamartempattidur_id
 * @property int $bpjs_id
 * @property int $status_ranap
 * @property int $pasienbatalperiksa_id
 * @property string $tgl_pindahkamar
 * @property int $status_verifikasi
 * @property bool $is_skd
 */
class PasienAdmisi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienadmisi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'caramasuk_id', 'ruangan_id', 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id', 'bpjs_id', 'status_ranap', 'pasienbatalperiksa_id', 'status_verifikasi'], 'default', 'value' => null],
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'caramasuk_id', 'ruangan_id', 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id', 'bpjs_id', 'status_ranap', 'pasienbatalperiksa_id', 'status_verifikasi'], 'integer'],
            [['carabayar_id', 'penjamin_id', 'pasien_id', 'ruangan_id', 'pendaftaran_id'], 'required'],
            [['tgl_admisi', 'tgl_pendaftaran', 'tgl_pulang', 'rencana_pulang', 'created_date', 'last_modified_date', 'deleted_date', 'tgl_pindahkamar'], 'safe'],
            [['status_keluar', 'rawat_gabung', 'is_deleted', 'is_active', 'is_skd'], 'boolean'],
            [['additional_data'], 'string'],
            [['kunjungan'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'shift_id' => 'Shift ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'pasien_id' => 'Pasien ID',
            'caramasuk_id' => 'Caramasuk ID',
            'ruangan_id' => 'Ruangan ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'bookingkamar_id' => 'Bookingkamar ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pegawai_id' => 'Pegawai ID',
            'tgl_admisi' => 'Tgl Admisi',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tgl_pulang' => 'Tgl Pulang',
            'kunjungan' => 'Kunjungan',
            'status_keluar' => 'Status Keluar',
            'rawat_gabung' => 'Rawat Gabung',
            'rencana_pulang' => 'Rencana Pulang',
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
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'bpjs_id' => 'Bpjs ID',
            'status_ranap' => 'Status Ranap',
            'pasienbatalperiksa_id' => 'Pasienbatalperiksa ID',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
            'status_verifikasi' => 'Status Verifikasi',
            'is_skd' => 'Is Skd',
        ];
    }
}
