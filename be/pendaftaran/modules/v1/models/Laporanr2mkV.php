<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanr2mk_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $pasienadmisi_id
 * @property string $no_registrasi
 * @property string $no_rekam_medik
 * @property string $tgl_registrasi
 * @property string $nama_dokter
 * @property string $nama_pasien
 * @property string $alamat_pasien
 * @property string $tanggal_lahir
 * @property string $tempat_lahir
 * @property string $umur
 * @property string $jeniskelamin
 * @property string $kebangsaan
 * @property string $suku_nama
 * @property string $no_identitas_pasien
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $no_asuransi_pasien
 * @property string $no_telepon_pasien
 * @property string $pemberitahuan
 * @property string $penanggungjawab_nama
 * @property string $penanggungjawab_alamat
 * @property string $hubungankeluarga
 * @property string $no_identitas_penanggung
 * @property string $pendidikan_pasien
 * @property string $prosedurmasuk_rs
 * @property string $pekerjaan_nama
 * @property string $alamat_kantor
 * @property string $dokter_pengirim
 * @property string $golongandarah
 * @property string $kelas
 * @property string $ruangan_nama
 * @property string $no_tempattidur
 * @property string $diagnosa_masuk
 * @property string $diag_utama_kode
 * @property string $diag_utama
 * @property string $diag_penyerta_kode
 * @property string $diag_penyerta
 * @property string $carakeluar_nama
 * @property string $penyulit
 * @property string $penyebab_kematian
 * @property string $tgl_jam
 * @property string $operasi_tindakan
 * @property int $lama_rawat
 * @property string $infeksi_nosokomial
 * @property string $tgl_keluar
 * @property string $alergi_obat
 * @property string $alergi_lainnya
 * @property bool $is_stoppasientitipan
 * @property int $kelas_ditagihkan_id
 * @property string $kelas_ditagihkan_nama
 * @property bool $is_pasientitipan
 * @property bool $is_pasientitipan_pk
 */
class Laporanr2mkV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanr2mk_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'lama_rawat', 'kelas_ditagihkan_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'lama_rawat', 'kelas_ditagihkan_id'], 'integer'],
            [['tgl_registrasi', 'tanggal_lahir', 'tgl_keluar'], 'safe'],
            [['alamat_pasien', 'jeniskelamin', 'kebangsaan', 'no_identitas_pasien', 'statusperkawinan', 'agama', 'pemberitahuan', 'penanggungjawab_alamat', 'alamat_kantor', 'golongandarah', 'diagnosa_masuk', 'penyulit', 'penyebab_kematian', 'tgl_jam', 'operasi_tindakan', 'infeksi_nosokomial', 'alergi_obat', 'alergi_lainnya'], 'string'],
            [['is_stoppasientitipan', 'is_pasientitipan', 'is_pasientitipan_pk'], 'boolean'],
            [['no_registrasi'], 'string', 'max' => 20],
            [['no_rekam_medik', 'carakeluar_nama'], 'string', 'max' => 100],
            [['nama_dokter', 'nama_pasien', 'suku_nama', 'no_asuransi_pasien', 'penanggungjawab_nama', 'hubungankeluarga', 'no_identitas_penanggung', 'pendidikan_pasien', 'prosedurmasuk_rs', 'pekerjaan_nama', 'dokter_pengirim', 'kelas', 'ruangan_nama', 'kelas_ditagihkan_nama'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['umur'], 'string', 'max' => 30],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['no_tempattidur'], 'string', 'max' => 255],
            [['diag_utama_kode', 'diag_penyerta_kode'], 'string', 'max' => 10],
            [['diag_utama', 'diag_penyerta'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_registrasi' => 'No Registrasi',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_registrasi' => 'Tgl Registrasi',
            'nama_dokter' => 'Nama Dokter',
            'nama_pasien' => 'Nama Pasien',
            'alamat_pasien' => 'Alamat Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'tempat_lahir' => 'Tempat Lahir',
            'umur' => 'Umur',
            'jeniskelamin' => 'Jeniskelamin',
            'kebangsaan' => 'Kebangsaan',
            'suku_nama' => 'Suku Nama',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'statusperkawinan' => 'Statusperkawinan',
            'agama' => 'Agama',
            'no_asuransi_pasien' => 'No Asuransi Pasien',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'pemberitahuan' => 'Pemberitahuan',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'penanggungjawab_alamat' => 'Penanggungjawab Alamat',
            'hubungankeluarga' => 'Hubungankeluarga',
            'no_identitas_penanggung' => 'No Identitas Penanggung',
            'pendidikan_pasien' => 'Pendidikan Pasien',
            'prosedurmasuk_rs' => 'Prosedurmasuk Rs',
            'pekerjaan_nama' => 'Pekerjaan Nama',
            'alamat_kantor' => 'Alamat Kantor',
            'dokter_pengirim' => 'Dokter Pengirim',
            'golongandarah' => 'Golongandarah',
            'kelas' => 'Kelas',
            'ruangan_nama' => 'Ruangan Nama',
            'no_tempattidur' => 'No Tempattidur',
            'diagnosa_masuk' => 'Diagnosa Masuk',
            'diag_utama_kode' => 'Diag Utama Kode',
            'diag_utama' => 'Diag Utama',
            'diag_penyerta_kode' => 'Diag Penyerta Kode',
            'diag_penyerta' => 'Diag Penyerta',
            'carakeluar_nama' => 'Carakeluar Nama',
            'penyulit' => 'Penyulit',
            'penyebab_kematian' => 'Penyebab Kematian',
            'tgl_jam' => 'Tgl Jam',
            'operasi_tindakan' => 'Operasi Tindakan',
            'lama_rawat' => 'Lama Rawat',
            'infeksi_nosokomial' => 'Infeksi Nosokomial',
            'tgl_keluar' => 'Tgl Keluar',
            'alergi_obat' => 'Alergi Obat',
            'alergi_lainnya' => 'Alergi Lainnya',
            'is_stoppasientitipan' => 'Is Stoppasientitipan',
            'kelas_ditagihkan_id' => 'Kelas Ditagihkan ID',
            'kelas_ditagihkan_nama' => 'Kelas Ditagihkan Nama',
            'is_pasientitipan' => 'Is Pasientitipan',
            'is_pasientitipan_pk' => 'Is Pasientitipan Pk',
        ];
    }
}
