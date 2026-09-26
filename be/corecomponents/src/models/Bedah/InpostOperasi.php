<?php

namespace Doco\models\Bedah;

use Yii;

/**
 * This is the model class for table "inpostoperasi_t".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property bool $is_surgicalsavety
 * @property int $dokterbedah_id
 * @property int $dokteranastesi_id
 * @property string $masuk_kamar
 * @property string $mulai_anastesi
 * @property string $selesai_anastesi
 * @property string $mulai_operasi
 * @property string $selesai_operasi
 * @property string $set_instrumen SET obatalkes_id, obatalkes_nama WHERE jenisobatalkes='alkes'
 * @property int $penunjang_khusus_id obatalkes_id WHERE jenisobatalkes='alkes'
 * @property string $perlengkapan_pribadi
 * @property bool $is_diathermy
 * @property int $kondisi_kulit_sebelum lookup_m.lookup_type='keadaan_kulit'
 * @property int $kondisi_kulit_setelah lookup_m.lookup_type='keadaan_kulit'
 * @property int $posisi_operasi lookup_m.lookup_type='posisi_operasi'
 * @property string $kateter_urin
 * @property int $pencucian_operasi lookup_m.lookup_type='pencucian_operasi'
 * @property int $posisi_elektroda lookup_m.lookup_type='posisi_elektroda'
 * @property string $fiksasi_balon
 * @property string $pemakaian_implan
 * @property string $lokasi_drainvacum
 * @property string $lokasi_drainpenrose
 * @property string $lokasi_drainselang
 * @property bool $is_jaringantubuh
 * @property string $jenis_jaringan
 * @property bool $is_diserahkan
 * @property string $penerima
 * @property int $pegawai_pemberi_id
 * @property bool $is_recovery ---batas post
 * @property string $jam_masuk_rec
 * @property string $jam_keluar_rec
 * @property int $kembali_ruangan_id
 * @property int $kesadaran_umum lookup_m.lookup_type='kesadaran_umum'
 * @property string $kesadaran_umum_lain jika 'LAINNYA'
 * @property int $tingkat_kesadaran lookup_m.lookup_type='tingkat_kesadaran'
 * @property string $tingkat_kesadaran_lain jika 'LAINNYA'
 * @property int $jalan_napas lookup_m.lookup_type='jalan_napas'
 * @property string $jalan_napas_lain jika 'LAINNYA'
 * @property int $terapi_oksigen lookup_m.lookup_type='terapi_oksigen'
 * @property string $terapi_oksigen_lain jika 'LAINNYA'
 * @property int $l_mnt
 * @property int $kulit_datang lookup_m.lookup_type='keadaan_kulit'
 * @property string $kulit_datang_lain jika 'LAINNYA'
 * @property int $kulit_keluar lookup_m.lookup_type='keadaan_kulit'
 * @property string $kulit_keluar_lain jika 'LAINNYA'
 * @property int $sirkulasi_badan lookup_m.lookup_type='sirkulasi_badan'
 * @property string $sirkulasi_badan_lain jika 'LAINNYA'
 * @property string $area_luka
 * @property bool $is_skrining_nyeri
 * @property string $ket_skrining
 * @property int $skala_nyeri
 * @property string $lokasi
 * @property int $metode_nyeri lookup_m.lookup_type='metode_nyeri'
 * @property string $resiko_jatuh
 * @property string $barang_pasien
 * @property bool $is_pasanginfus
 * @property string $keterangan
 * @property string $pemberitahu_perawat
 * @property string $perawat_datang
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
class InpostOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'dokterbedah_id', 'dokteranastesi_id', 'penunjang_khusus_id', 'kondisi_kulit_sebelum', 'kondisi_kulit_setelah', 'posisi_operasi', 'pencucian_operasi', 'posisi_elektroda', 'pegawai_pemberi_id', 'kembali_ruangan_id', 'kesadaran_umum', 'tingkat_kesadaran', 'jalan_napas', 'terapi_oksigen', 'l_mnt', 'kulit_datang', 'kulit_keluar', 'sirkulasi_badan', 'skala_nyeri', 'metode_nyeri', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'dokterbedah_id', 'dokteranastesi_id', 'penunjang_khusus_id', 'kondisi_kulit_sebelum', 'kondisi_kulit_setelah', 'posisi_operasi', 'pencucian_operasi', 'posisi_elektroda', 'pegawai_pemberi_id', 'kembali_ruangan_id', 'kesadaran_umum', 'tingkat_kesadaran', 'jalan_napas', 'terapi_oksigen', 'l_mnt', 'kulit_datang', 'kulit_keluar', 'sirkulasi_badan', 'skala_nyeri', 'metode_nyeri', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_surgicalsavety', 'is_diathermy', 'is_jaringantubuh', 'is_diserahkan', 'is_recovery', 'is_skrining_nyeri', 'is_pasanginfus', 'is_deleted', 'is_active'], 'boolean'],
            [['masuk_kamar', 'mulai_anastesi', 'selesai_anastesi', 'mulai_operasi', 'selesai_operasi', 'jam_masuk_rec', 'jam_keluar_rec', 'pemberitahu_perawat', 'perawat_datang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['set_instrumen', 'perlengkapan_pribadi', 'pemakaian_implan', 'barang_pasien', 'keterangan', 'additional_data'], 'string'],
            [['kateter_urin', 'fiksasi_balon'], 'string', 'max' => 100],
            [['lokasi_drainvacum', 'lokasi_drainpenrose', 'lokasi_drainselang', 'jenis_jaringan', 'penerima', 'kesadaran_umum_lain', 'tingkat_kesadaran_lain', 'terapi_oksigen_lain', 'kulit_datang_lain', 'kulit_keluar_lain', 'sirkulasi_badan_lain', 'area_luka', 'ket_skrining', 'lokasi', 'resiko_jatuh'], 'string', 'max' => 255],
            [['jalan_napas_lain'], 'string', 'max' => 32],
            [['inpostoperasi_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'is_surgicalsavety' => 'Is Surgicalsavety',
            'dokterbedah_id' => 'Dokterbedah ID',
            'dokteranastesi_id' => 'Dokteranastesi ID',
            'masuk_kamar' => 'Masuk Kamar',
            'mulai_anastesi' => 'Mulai Anastesi',
            'selesai_anastesi' => 'Selesai Anastesi',
            'mulai_operasi' => 'Mulai Operasi',
            'selesai_operasi' => 'Selesai Operasi',
            'set_instrumen' => 'Set Instrumen',
            'penunjang_khusus_id' => 'Penunjang Khusus ID',
            'perlengkapan_pribadi' => 'Perlengkapan Pribadi',
            'is_diathermy' => 'Is Diathermy',
            'kondisi_kulit_sebelum' => 'Kondisi Kulit Sebelum',
            'kondisi_kulit_setelah' => 'Kondisi Kulit Setelah',
            'posisi_operasi' => 'Posisi Operasi',
            'kateter_urin' => 'Kateter Urin',
            'pencucian_operasi' => 'Pencucian Operasi',
            'posisi_elektroda' => 'Posisi Elektroda',
            'fiksasi_balon' => 'Fiksasi Balon',
            'pemakaian_implan' => 'Pemakaian Implan',
            'lokasi_drainvacum' => 'Lokasi Drainvacum',
            'lokasi_drainpenrose' => 'Lokasi Drainpenrose',
            'lokasi_drainselang' => 'Lokasi Drainselang',
            'is_jaringantubuh' => 'Is Jaringantubuh',
            'jenis_jaringan' => 'Jenis Jaringan',
            'is_diserahkan' => 'Is Diserahkan',
            'penerima' => 'Penerima',
            'pegawai_pemberi_id' => 'Pegawai Pemberi ID',
            'is_recovery' => 'Is Recovery',
            'jam_masuk_rec' => 'Jam Masuk Rec',
            'jam_keluar_rec' => 'Jam Keluar Rec',
            'kembali_ruangan_id' => 'Kembali Ruangan ID',
            'kesadaran_umum' => 'Kesadaran Umum',
            'kesadaran_umum_lain' => 'Kesadaran Umum Lain',
            'tingkat_kesadaran' => 'Tingkat Kesadaran',
            'tingkat_kesadaran_lain' => 'Tingkat Kesadaran Lain',
            'jalan_napas' => 'Jalan Napas',
            'jalan_napas_lain' => 'Jalan Napas Lain',
            'terapi_oksigen' => 'Terapi Oksigen',
            'terapi_oksigen_lain' => 'Terapi Oksigen Lain',
            'l_mnt' => 'L Mnt',
            'kulit_datang' => 'Kulit Datang',
            'kulit_datang_lain' => 'Kulit Datang Lain',
            'kulit_keluar' => 'Kulit Keluar',
            'kulit_keluar_lain' => 'Kulit Keluar Lain',
            'sirkulasi_badan' => 'Sirkulasi Badan',
            'sirkulasi_badan_lain' => 'Sirkulasi Badan Lain',
            'area_luka' => 'Area Luka',
            'is_skrining_nyeri' => 'Is Skrining Nyeri',
            'ket_skrining' => 'Ket Skrining',
            'skala_nyeri' => 'Skala Nyeri',
            'lokasi' => 'Lokasi',
            'metode_nyeri' => 'Metode Nyeri',
            'resiko_jatuh' => 'Resiko Jatuh',
            'barang_pasien' => 'Barang Pasien',
            'is_pasanginfus' => 'Is Pasanginfus',
            'keterangan' => 'Keterangan',
            'pemberitahu_perawat' => 'Pemberitahu Perawat',
            'perawat_datang' => 'Perawat Datang',
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
    public function getSetPenunjang()
    {
        return $this->hasOne(InfoObatAlkesView::className(), ['obatalkes_id' => 'penunjang_khusus_id']);
    }
    public function getSetInstrumen()
    {
        return $this->hasOne(InfoObatAlkesView::className(), ['obatalkes_id' => 'set_instrumen']);
    }
    public function getPegawaiPenerima()
    {
        return $this->hasOne(PegawaiView::className(), ['pegawai_id' => 'pegawai_pemberi_id']);
    }

}
