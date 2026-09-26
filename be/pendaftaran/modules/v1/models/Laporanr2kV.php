<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanr2k_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasien_id
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
 * @property string $namaperusahaan
 * @property string $no_asuransi_pasien
 * @property string $no_telepon_pasien
 * @property string $pendidikan_nama
 * @property string $pekerjaan_nama
 * @property string $pemberitahuan
 * @property string $penanggungjawab_nama
 * @property string $no_identitas_penanggung
 * @property string $penanggungjawab_alamat
 * @property string $hubungankeluarga
 * @property string $alamat_kantor
 * @property string $dokter_pengirim
 * @property string $golongandarah
 * @property string $diet
 * @property string $alergi_obat
 * @property string $alergi_lainnya
 */
class Laporanr2kV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanr2k_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id'], 'integer'],
            [['tgl_registrasi', 'tanggal_lahir'], 'safe'],
            [['alamat_pasien', 'jeniskelamin', 'kebangsaan', 'no_identitas_pasien', 'statusperkawinan', 'agama', 'pemberitahuan', 'penanggungjawab_alamat', 'alamat_kantor', 'golongandarah', 'diet', 'alergi_obat', 'alergi_lainnya'], 'string'],
            [['no_registrasi'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 100],
            [['nama_dokter', 'nama_pasien', 'suku_nama', 'namaperusahaan', 'no_asuransi_pasien', 'pendidikan_nama', 'pekerjaan_nama', 'penanggungjawab_nama', 'no_identitas_penanggung', 'hubungankeluarga', 'dokter_pengirim'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['umur'], 'string', 'max' => 30],
            [['no_telepon_pasien'], 'string', 'max' => 15],
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
            'namaperusahaan' => 'Namaperusahaan',
            'no_asuransi_pasien' => 'No Asuransi Pasien',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'pendidikan_nama' => 'Pendidikan Nama',
            'pekerjaan_nama' => 'Pekerjaan Nama',
            'pemberitahuan' => 'Pemberitahuan',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'no_identitas_penanggung' => 'No Identitas Penanggung',
            'penanggungjawab_alamat' => 'Penanggungjawab Alamat',
            'hubungankeluarga' => 'Hubungankeluarga',
            'alamat_kantor' => 'Alamat Kantor',
            'dokter_pengirim' => 'Dokter Pengirim',
            'golongandarah' => 'Golongandarah',
            'diet' => 'Diet',
            'alergi_obat' => 'Alergi Obat',
            'alergi_lainnya' => 'Alergi Lainnya',
        ];
    }
}
