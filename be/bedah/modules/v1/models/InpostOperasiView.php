<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property bool $is_surgicalsavety
 * @property int $dokterbedah_id
 * @property string $dok_bedah
 * @property int $dokteranastesi_id
 * @property string $dok_anastesi
 * @property int $timoperasi_id
 * @property int $posisi_tim
 * @property string $posisi
 * @property string $pegawai_posisi
 * @property string $masuk_kamar
 * @property string $mulai_anastesi
 * @property string $selesai_anastesi
 * @property string $mulai_operasi
 * @property string $selesai_operasi
 * @property int $pelayananoperasi_id
 * @property int $daftartindakan_id
 * @property string $nama_operasi
 * @property bool $is_cyto
 * @property int $jenis_luka
 * @property string $jenis_luka_n
 * @property int $golonganoperasi_id
 * @property string $jenis_operasi
 * @property int $jenisanastesi_id
 * @property string $jenisanastesi_nama
 * @property string $set_instrumen
 * @property int $penunjang_khusus_id
 * @property string $perlengkapan_pribadi
 * @property bool $is_diathermy
 * @property int $kondisi_kulit_sebelum
 * @property string $kulit_sebelum
 * @property int $kondisi_kulit_setelah
 * @property string $kulit_setelah
 * @property int $posisi_operasi
 * @property string $posisi_op
 * @property string $kateter_urin
 * @property int $pencucian_operasi
 * @property string $cuci_operasi
 * @property int $posisi_elektroda
 * @property string $pos_elektroda
 * @property string $fiksasi_balon
 * @property string $pemakaian_implan
 * @property int $bmhpoperasi_id
 * @property int $obatalkes_id
 * @property string $jenis_alat_bmhp
 * @property int $persediaan
 * @property int $tambahan
 * @property int $terpakai
 * @property int $sisa
 * @property bool $is_ditagihkan
 * @property string $kegiatan
 * @property string $cairan_masuk
 * @property string $cairan_keluar
 * @property string $keterangan
 * @property string $lokasi_drainvacum
 * @property string $lokasi_drainpenrose
 * @property string $lokasi_drainselang
 * @property bool $is_jaringantubuh
 * @property string $jenis_jaringan
 * @property bool $is_diserahkan
 * @property string $penerima
 * @property int $pegawai_pemberi_id
 * @property string $pegawai_pemberi
 * @property int $jenis_alat
 * @property int $jumlah
 * @property string $lokasi_alat
 * @property string $pemeriksaan_pelengkap
 * @property string $nama_jaringan
 * @property int $ukuran
 * @property int $konsultasitindakan_id
 * @property string $bagian_tubuh
 * @property int $dokter_id
 * @property string $dokter_konsul
 * @property string $alasan
 * @property int $tindakan_konsul_id
 * @property string $tindakan_konsul
 * @property bool $is_recovery
 * @property string $jam_masuk_rec
 * @property string $jam_keluar_rec
 * @property int $kembali_ruangan_id
 * @property int $kesadaran_umum
 * @property string $kesadaran_umum_lain
 * @property int $tingkat_kesadaran
 * @property string $tingkat_kesadaran_lain
 * @property int $jalan_napas
 * @property string $jalan_napas_lain
 * @property int $terapi_oksigen
 * @property string $terapi_oksigen_lain
 * @property int $l_mnt
 * @property int $kulit_datang
 * @property string $kulit_datang_lain
 * @property int $kulit_keluar
 * @property string $kulit_keluar_lain
 * @property int $sirkulasi_badan
 * @property string $sirkulasi_badan_lain
 * @property string $area_luka
 * @property bool $is_skrining_nyeri
 * @property string $ket_skrining
 * @property int $skala_nyeri
 * @property string $lokasi
 * @property int $metode_nyeri
 * @property string $resiko_jatuh
 * @property string $barang_pasien
 * @property bool $is_pasanginfus
 * @property int $jeniscairan_id
 * @property string $jns_cairan
 * @property string $tgl_pemasangan
 * @property int $jumlah_tetes
 * @property string $keterangan_post
 * @property string $pemberitahu_perawat
 * @property string $perawat_datang
 * @property int $tindakanpelayanan_id
 * @property int $tindakan_op_id
 * @property string $tindakan_op
 */
class InpostOperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'dokterbedah_id', 'dokteranastesi_id', 'timoperasi_id', 'posisi_tim', 'pelayananoperasi_id', 'daftartindakan_id', 'jenis_luka', 'golonganoperasi_id', 'jenisanastesi_id', 'penunjang_khusus_id', 'kondisi_kulit_sebelum', 'kondisi_kulit_setelah', 'posisi_operasi', 'pencucian_operasi', 'posisi_elektroda', 'bmhpoperasi_id', 'obatalkes_id', 'persediaan', 'tambahan', 'terpakai', 'sisa', 'pegawai_pemberi_id', 'jenis_alat', 'jumlah', 'ukuran', 'konsultasitindakan_id', 'dokter_id', 'tindakan_konsul_id', 'kembali_ruangan_id', 'kesadaran_umum', 'tingkat_kesadaran', 'jalan_napas', 'terapi_oksigen', 'l_mnt', 'kulit_datang', 'kulit_keluar', 'sirkulasi_badan', 'skala_nyeri', 'metode_nyeri', 'jeniscairan_id', 'jumlah_tetes', 'tindakanpelayanan_id', 'tindakan_op_id'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'dokterbedah_id', 'dokteranastesi_id', 'timoperasi_id', 'posisi_tim', 'pelayananoperasi_id', 'daftartindakan_id', 'jenis_luka', 'golonganoperasi_id', 'jenisanastesi_id', 'penunjang_khusus_id', 'kondisi_kulit_sebelum', 'kondisi_kulit_setelah', 'posisi_operasi', 'pencucian_operasi', 'posisi_elektroda', 'bmhpoperasi_id', 'obatalkes_id', 'persediaan', 'tambahan', 'terpakai', 'sisa', 'pegawai_pemberi_id', 'jenis_alat', 'jumlah', 'ukuran', 'konsultasitindakan_id', 'dokter_id', 'tindakan_konsul_id', 'kembali_ruangan_id', 'kesadaran_umum', 'tingkat_kesadaran', 'jalan_napas', 'terapi_oksigen', 'l_mnt', 'kulit_datang', 'kulit_keluar', 'sirkulasi_badan', 'skala_nyeri', 'metode_nyeri', 'jeniscairan_id', 'jumlah_tetes', 'tindakanpelayanan_id', 'tindakan_op_id'], 'integer'],
            [['is_surgicalsavety', 'is_cyto', 'is_diathermy', 'is_ditagihkan', 'is_jaringantubuh', 'is_diserahkan', 'is_recovery', 'is_skrining_nyeri', 'is_pasanginfus'], 'boolean'],
            [['masuk_kamar', 'mulai_anastesi', 'selesai_anastesi', 'mulai_operasi', 'selesai_operasi', 'jam_masuk_rec', 'jam_keluar_rec', 'tgl_pemasangan', 'pemberitahu_perawat', 'perawat_datang'], 'safe'],
            [['set_instrumen', 'perlengkapan_pribadi', 'pemakaian_implan', 'keterangan', 'barang_pasien', 'keterangan_post'], 'string'],
            [['dok_bedah', 'dok_anastesi', 'pegawai_posisi', 'jenisanastesi_nama', 'pegawai_pemberi', 'dokter_konsul'], 'string', 'max' => 50],
            [['posisi', 'nama_operasi', 'jenis_luka_n', 'kulit_sebelum', 'kulit_setelah', 'posisi_op', 'cuci_operasi', 'pos_elektroda', 'pemeriksaan_pelengkap', 'tindakan_konsul', 'tindakan_op'], 'string', 'max' => 200],
            [['jenis_operasi', 'kateter_urin', 'fiksasi_balon'], 'string', 'max' => 100],
            [['jenis_alat_bmhp', 'kegiatan', 'cairan_masuk', 'cairan_keluar', 'lokasi_drainvacum', 'lokasi_drainpenrose', 'lokasi_drainselang', 'jenis_jaringan', 'penerima', 'lokasi_alat', 'nama_jaringan', 'bagian_tubuh', 'alasan', 'kesadaran_umum_lain', 'tingkat_kesadaran_lain', 'terapi_oksigen_lain', 'kulit_datang_lain', 'kulit_keluar_lain', 'sirkulasi_badan_lain', 'area_luka', 'ket_skrining', 'lokasi', 'resiko_jatuh', 'jns_cairan'], 'string', 'max' => 255],
            [['jalan_napas_lain'], 'string', 'max' => 32],
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
            'dok_bedah' => 'Dok Bedah',
            'dokteranastesi_id' => 'Dokteranastesi ID',
            'dok_anastesi' => 'Dok Anastesi',
            'timoperasi_id' => 'Timoperasi ID',
            'posisi_tim' => 'Posisi Tim',
            'posisi' => 'Posisi',
            'pegawai_posisi' => 'Pegawai Posisi',
            'masuk_kamar' => 'Masuk Kamar',
            'mulai_anastesi' => 'Mulai Anastesi',
            'selesai_anastesi' => 'Selesai Anastesi',
            'mulai_operasi' => 'Mulai Operasi',
            'selesai_operasi' => 'Selesai Operasi',
            'pelayananoperasi_id' => 'Pelayananoperasi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'nama_operasi' => 'Nama Operasi',
            'is_cyto' => 'Is Cyto',
            'jenis_luka' => 'Jenis Luka',
            'jenis_luka_n' => 'Jenis Luka N',
            'golonganoperasi_id' => 'Golonganoperasi ID',
            'jenis_operasi' => 'Jenis Operasi',
            'jenisanastesi_id' => 'Jenisanastesi ID',
            'jenisanastesi_nama' => 'Jenisanastesi Nama',
            'set_instrumen' => 'Set Instrumen',
            'penunjang_khusus_id' => 'Penunjang Khusus ID',
            'perlengkapan_pribadi' => 'Perlengkapan Pribadi',
            'is_diathermy' => 'Is Diathermy',
            'kondisi_kulit_sebelum' => 'Kondisi Kulit Sebelum',
            'kulit_sebelum' => 'Kulit Sebelum',
            'kondisi_kulit_setelah' => 'Kondisi Kulit Setelah',
            'kulit_setelah' => 'Kulit Setelah',
            'posisi_operasi' => 'Posisi Operasi',
            'posisi_op' => 'Posisi Op',
            'kateter_urin' => 'Kateter Urin',
            'pencucian_operasi' => 'Pencucian Operasi',
            'cuci_operasi' => 'Cuci Operasi',
            'posisi_elektroda' => 'Posisi Elektroda',
            'pos_elektroda' => 'Pos Elektroda',
            'fiksasi_balon' => 'Fiksasi Balon',
            'pemakaian_implan' => 'Pemakaian Implan',
            'bmhpoperasi_id' => 'Bmhpoperasi ID',
            'obatalkes_id' => 'Obatalkes ID',
            'jenis_alat_bmhp' => 'Jenis Alat Bmhp',
            'persediaan' => 'Persediaan',
            'tambahan' => 'Tambahan',
            'terpakai' => 'Terpakai',
            'sisa' => 'Sisa',
            'is_ditagihkan' => 'Is Ditagihkan',
            'kegiatan' => 'Kegiatan',
            'cairan_masuk' => 'Cairan Masuk',
            'cairan_keluar' => 'Cairan Keluar',
            'keterangan' => 'Keterangan',
            'lokasi_drainvacum' => 'Lokasi Drainvacum',
            'lokasi_drainpenrose' => 'Lokasi Drainpenrose',
            'lokasi_drainselang' => 'Lokasi Drainselang',
            'is_jaringantubuh' => 'Is Jaringantubuh',
            'jenis_jaringan' => 'Jenis Jaringan',
            'is_diserahkan' => 'Is Diserahkan',
            'penerima' => 'Penerima',
            'pegawai_pemberi_id' => 'Pegawai Pemberi ID',
            'pegawai_pemberi' => 'Pegawai Pemberi',
            'jenis_alat' => 'Jenis Alat',
            'jumlah' => 'Jumlah',
            'lokasi_alat' => 'Lokasi Alat',
            'pemeriksaan_pelengkap' => 'Pemeriksaan Pelengkap',
            'nama_jaringan' => 'Nama Jaringan',
            'ukuran' => 'Ukuran',
            'konsultasitindakan_id' => 'Konsultasitindakan ID',
            'bagian_tubuh' => 'Bagian Tubuh',
            'dokter_id' => 'Dokter ID',
            'dokter_konsul' => 'Dokter Konsul',
            'alasan' => 'Alasan',
            'tindakan_konsul_id' => 'Tindakan Konsul ID',
            'tindakan_konsul' => 'Tindakan Konsul',
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
            'jeniscairan_id' => 'Jeniscairan ID',
            'jns_cairan' => 'Jns Cairan',
            'tgl_pemasangan' => 'Tgl Pemasangan',
            'jumlah_tetes' => 'Jumlah Tetes',
            'keterangan_post' => 'Keterangan Post',
            'pemberitahu_perawat' => 'Pemberitahu Perawat',
            'perawat_datang' => 'Perawat Datang',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'tindakan_op_id' => 'Tindakan Op ID',
            'tindakan_op' => 'Tindakan Op',
        ];
    }
}
