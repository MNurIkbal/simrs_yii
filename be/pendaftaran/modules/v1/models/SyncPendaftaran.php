<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sync_pendaftaran".
 *
 * @property int $pendaftaran_id
 * @property string $no_reg
 * @property string $tgl_pendaftaran
 * @property string $jam_pendaftaran
 * @property int $pasien_id
 * @property string $kode_cm
 * @property int $ruangan_id
 * @property string $kode_subunit
 * @property string $kunjungan
 * @property string $kode_stscm
 * @property string $kode_pendidikan
 * @property string $kode_pekerjaan
 * @property string $kode_stskawin
 * @property int $rujukan_id
 * @property int $bpjs_id
 * @property int $asalrujukan_id
 * @property string $kode_rujukan
 * @property int $rujukandari_id
 * @property string $kode_ppk
 * @property string $nama_perujuk
 * @property string $kode_stsbayar
 * @property string $nama_pengantar
 * @property string $alamat_pengantar
 * @property string $telp_pengantar
 * @property string $hp_pengantar
 * @property string $hub_pengantar
 * @property string $nama_penanggung
 * @property string $alamat_penanggung
 * @property string $telp_penanggung
 * @property string $hp_penanggung
 * @property string $hub_penanggung
 * @property string $tgl_commit
 * @property string $keterangan
 * @property string $cek
 * @property string $petugas
 * @property string $nprs_pjawab
 * @property int $hak_kelas
 * @property string $kode_subunitasal
 * @property string $diag_awal
 * @property int $is_cloned
 * @property string $kode_perusahaan
 * @property string $no_kartu
 * @property string $no_sep
 */
class SyncPendaftaran extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sync_pendaftaran';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'ruangan_id', 'rujukan_id', 'bpjs_id', 'asalrujukan_id', 'rujukandari_id', 'hak_kelas', 'is_cloned'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'ruangan_id', 'rujukan_id', 'bpjs_id', 'asalrujukan_id', 'rujukandari_id', 'hak_kelas', 'is_cloned'], 'integer'],
            [['tgl_pendaftaran', 'jam_pendaftaran', 'kode_rujukan', 'kode_ppk', 'nama_perujuk', 'alamat_pengantar', 'alamat_penanggung', 'keterangan', 'cek', 'diag_awal'], 'string'],
            [['tgl_commit'], 'safe'],
            [['no_reg'], 'string', 'max' => 20],
            [['kode_cm'], 'string', 'max' => 10],
            [['kode_subunit', 'kode_subunitasal', 'no_sep'], 'string', 'max' => 100],
            [['kunjungan', 'kode_stscm', 'kode_pendidikan', 'kode_pekerjaan', 'kode_stskawin', 'kode_stsbayar', 'nama_pengantar', 'nama_penanggung', 'no_kartu'], 'string', 'max' => 50],
            [['telp_pengantar', 'hp_pengantar', 'telp_penanggung', 'hp_penanggung'], 'string', 'max' => 15],
            [['hub_pengantar', 'hub_penanggung'], 'string', 'max' => 200],
            [['petugas', 'nprs_pjawab'], 'string', 'max' => 30],
            [['kode_perusahaan'], 'string', 'max' => 70],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_reg' => 'No Reg',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'jam_pendaftaran' => 'Jam Pendaftaran',
            'pasien_id' => 'Pasien ID',
            'kode_cm' => 'Kode Cm',
            'ruangan_id' => 'Ruangan ID',
            'kode_subunit' => 'Kode Subunit',
            'kunjungan' => 'Kunjungan',
            'kode_stscm' => 'Kode Stscm',
            'kode_pendidikan' => 'Kode Pendidikan',
            'kode_pekerjaan' => 'Kode Pekerjaan',
            'kode_stskawin' => 'Kode Stskawin',
            'rujukan_id' => 'Rujukan ID',
            'bpjs_id' => 'Bpjs ID',
            'asalrujukan_id' => 'Asalrujukan ID',
            'kode_rujukan' => 'Kode Rujukan',
            'rujukandari_id' => 'Rujukandari ID',
            'kode_ppk' => 'Kode Ppk',
            'nama_perujuk' => 'Nama Perujuk',
            'kode_stsbayar' => 'Kode Stsbayar',
            'nama_pengantar' => 'Nama Pengantar',
            'alamat_pengantar' => 'Alamat Pengantar',
            'telp_pengantar' => 'Telp Pengantar',
            'hp_pengantar' => 'Hp Pengantar',
            'hub_pengantar' => 'Hub Pengantar',
            'nama_penanggung' => 'Nama Penanggung',
            'alamat_penanggung' => 'Alamat Penanggung',
            'telp_penanggung' => 'Telp Penanggung',
            'hp_penanggung' => 'Hp Penanggung',
            'hub_penanggung' => 'Hub Penanggung',
            'tgl_commit' => 'Tgl Commit',
            'keterangan' => 'Keterangan',
            'cek' => 'Cek',
            'petugas' => 'Petugas',
            'nprs_pjawab' => 'Nprs Pjawab',
            'hak_kelas' => 'Hak Kelas',
            'kode_subunitasal' => 'Kode Subunitasal',
            'diag_awal' => 'Diag Awal',
            'is_cloned' => 'Is Cloned',
            'kode_perusahaan' => 'Kode Perusahaan',
            'no_kartu' => 'No Kartu',
            'no_sep' => 'No Sep',
        ];
    }
}
