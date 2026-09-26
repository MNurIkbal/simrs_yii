<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pendaftaran_t".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property int $pasienpulang_id
 * @property int $pasienbatalperiksa_id
 * @property int $penanggungjawab_id
 * @property int $penjamin_id
 * @property int $shift_id
 * @property int $pasien_id
 * @property int $persalinan_id
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $caramasuk_id
 * @property int $pengirimanrm_id
 * @property int $peminjamanrm_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pembayaranpelayanan_id
 * @property int $kelaspelayanan_id
 * @property int $carabayar_id
 * @property int $pasienadmisi_id
 * @property int $kelompokumur_id
 * @property int $golonganumur_id
 * @property int $rujukan_id
 * @property int $antrian_id
 * @property int $karcis_id
 * @property int $ruangan_id
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_periksa
 * @property string $status_pasien
 * @property string $kunjungan
 * @property bool $alih_status
 * @property bool $by_phone
 * @property bool $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property string $tgl_selesaiperiksa
 * @property string $keterangan_pendaftaran
 * @property bool $nopendaftaran_aktif
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property string $tgl_renkontrol
 * @property bool $status_farmasi
 * @property bool $panggil_antrian
 * @property int $asuransipasien_id
 * @property string $tgl_akandilayani
 * @property string $statusdok_rekammedik
 * @property int $bpjs_id
 * @property int $sep_id
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

class Pendaftaran extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendaftaran_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_pendaftaran', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk'], 'required'],
            [['asuransipasien_id', 'tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_akandilayani', 'created_date','pegawai_id', 'last_modified_date', 'deleted_date','is_aps','bpjs_id', 'pj_nama', 'pj_no_identitas', 'pj_alamat', 'pj_no_telepon', 'pj_tempat_lahir', 'keterangan_pendaftaran','status_konfirmasi'], 'safe'],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'bpjs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'bpjs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'nopendaftaran_aktif', 'status_farmasi', 'panggil_antrian', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_pendaftaran', 'additional_data'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_urutantri'], 'string', 'max' => 6],
            [['transportasi', 'keadaan_masuk','status_pasien', 'kunjungan', 
                 'status_konfirmasi'], 'string', 'max' => 50],
            [['umur'], 'string', 'max' => 30],
            [['no_pendaftaran'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'tgl_buatjanji' => 'Tgl Buatjanji',
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'hari' => 'Hari',
            'tgl_jadwal' => 'Tgl Jadwal',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pasienpulang_id' => 'Pasienpulang ID',
            'pasienbatalperiksa_id' => 'Pasienbatalperiksa ID',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'penjamin_id' => 'Penjamin ID',
            'shift_id' => 'Shift ID',
            'pasien_id' => 'Pasien ID',
            'persalinan_id' => 'Persalinan ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'caramasuk_id' => 'Caramasuk ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'kelompokumur_id' => 'Kelompokumur ID',
            'golonganumur_id' => 'Golonganumur ID',
            'rujukan_id' => 'Rujukan ID',
            'antrian_id' => 'Antrian ID',
            'karcis_id' => 'Karcis ID',
            'ruangan_id' => 'Ruangan ID',
            'no_urutantri' => 'No Urutantri',
            'transportasi' => 'Transportasi',
            'keadaan_masuk' => 'Keadaan Masuk',
            'status_periksa' => 'Status Periksa',
            'status_pasien' => 'Status Pasien',
            'kunjungan' => 'Kunjungan',
            'alih_status' => 'Alih Status',
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => 'Kunjungan Rumah',
            'status_masuk' => 'Status Masuk',
            'umur' => 'Umur',
            'tgl_selesaiperiksa' => 'Tgl Selesaiperiksa',
            'keterangan_pendaftaran' => 'Keterangan Pendaftaran',
            'nopendaftaran_aktif' => 'Nopendaftaran Aktif',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'tgl_renkontrol' => 'Tgl Renkontrol',
            'status_farmasi' => 'Status Farmasi',
            'panggil_antrian' => 'Panggil Antrian',
            'asuransipasien_id' => 'Asuransipasien ID',
            'tgl_akandilayani' => 'Tgl Akandilayani',
            'statusdok_rekammedik' => 'Statusdok Rekammedik',
            'bpjs_id' => 'Bpjs ID',
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
