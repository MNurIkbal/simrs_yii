<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienpulang_t".
 *
 * @property int $pasienpulang_id
 * @property int $pasien_id
 * @property int $pasienbatalpulang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $carakeluar_id
 * @property int $kondisikeluar_id
 * @property string $tglpasienpulang
 * @property int $ruanganakhir_id
 * @property string $penerima_pasien
 * @property int $lama_rawat
 * @property string $satuan_lamarawat
 * @property bool $is_meninggal
 * @property string $keterangan_keluar
 * @property int $hari_perawatan
 * @property string $satuan_hariperawatan
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $tgl_meninggal
 * @property bool $is_rencanakontrol
 * @property string $tgl_rencanakontrol
 * @property int $pasiendirujukkeluar_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $status_jenazah
 * @property string $tgl_kremasi
 * @property string $nama_pemeriksa_jenazah
 * @property string $kualifikasi_pemeriksa
 * @property string $waktu_pemeriksaan_jenazah
 * @property string $dasar_diagnosis
 * @property string $kelompok_kematian
 * @property string $tempat_kematian
 * @property string $penyebab_langsung
 * @property string $penyebab_antara
 * @property string $penyebab_dasar
 * @property string $kondisi_lain
 * @property string $penyebab_utama_bayi
 * @property string $penyebab_utama_ibu
 * @property string $penyebab_lain_bayi
 * @property string $penyebab_lain_ibu
 * @property string $pihak_menerima
 * @property string $hubungan_penerima
 * @property string $infeksi
 * @property int $kamarruangan_jenis
 * @property int $dokterspesialis_id
 * @property string $catatan_tindakan
 * @property string $no_surat_kematian

 */

class PasienPulang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienpulang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_id','kondisikeluar_id'], 'required'],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'pasiendirujukkeluar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','infeksi', 'kamarruangan_jenis', 'dokterspesialis_id', 'catatan_tindakan'], 'default', 'value' => null],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'pasiendirujukkeluar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamarruangan_jenis', 'dokterspesialis_id'], 'integer'],
            [['tglpasienpulang', 'tgl_meninggal', 'tgl_rencanakontrol', 'created_date', 'last_modified_date', 'deleted_date', 'dpjp_id','tempattidurtujuan_id', 'catatan_lain','status_jenazah','tgl_kremasi','nama_pemeriksa_jenazah','kualifikasi_pemeriksa','waktu_pemeriksaan_jenazah','dasar_diagnosis','kelompok_kematian','tempat_kematian','penyebab_langsung','penyebab_antara','penyebab_dasar','kondisi_lain','penyebab_utama_bayi','penyebab_utama_ibu','penyebab_lain_bayi','penyebab_lain_ibu','pihak_menerima','hubungan_penerima','infeksi', 'kamarruangan_jenis','dokterspesialis_id', 'catatan_tindakan','no_surat_kematian'], 'safe'],
            [['is_meninggal', 'is_deleted', 'is_active', 'is_rencanakontrol'], 'boolean'],
            [['keterangan_keluar', 'additional_data'], 'string'],
            [['penerima_pasien'], 'string', 'max' => 100],
            [['satuan_lamarawat', 'satuan_hariperawatan'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienpulang_id' => 'Pasienpulang ID',
            'pasien_id' => 'Pasien ID',
            'pasienbatalpulang_id' => 'Pasienbatalpulang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'carakeluar_id' => 'Carakeluar ID',
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'tglpasienpulang' => 'Tglpasienpulang',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'penerima_pasien' => 'Penerima Pasien',
            'lama_rawat' => 'Lama Rawat',
            'satuan_lamarawat' => 'Satuan Lamarawat',
            'is_meninggal' => 'Is Meninggal',
            'keterangan_keluar' => 'Keterangan Keluar',
            'hari_perawatan' => 'Hari Perawatan',
            'satuan_hariperawatan' => 'Satuan Hariperawatan',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'tgl_meninggal' => 'Tgl Meninggal',
            'is_rencanakontrol' => 'Is Rencanakontrol',
            'tgl_rencanakontrol' => 'Tgl Rencanakontrol',
            'pasiendirujukkeluar_id' => 'Pasiendirujukkeluar ID',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'status_jenazah' => 'Status Jenazah',
            'infeksi' => 'Infeksi',
            'kamarruangan_jenis' => 'Jenis Kamar',
            'no_surat_kematian' => 'Nomor surat kematian',
        ];
    }

}
