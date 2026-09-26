<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienrad_v".
 *
 * @property string $tipe_pasien
 * @property int $pendaftaran_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $tglmasukpenunjang
 * @property string $no_pendaftaran
 * @property string $no_masukpenunjang
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $pegawai_id
 * @property string $dokter_penunjang
 * @property string $no_rujukan
 * @property int $asalrujukan_id
 * @property string $asalrujukan_nama
 * @property int $ruanganasal_id
 * @property string $ruangan_nama
 * @property string $status_periksa
 * @property string $no_antrian
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $umur
 * @property string $jeniskelamin
 * @property string $j_kelamin
 * @property string $tanggal_lahir
 * @property string $kuning
 * @property string $merah
 * @property string $ungu
 * @property string $coklat
 * @property string $tgl_rujukan
 * @property int $pasien_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property bool $is_bayar
 * @property string $status_penunjang
 */
class InfoPasienRadView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienrad_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe_pasien', 'no_rujukan', 'asalrujukan_nama', 'ruangan_nama', 'kuning', 'merah', 'ungu', 'coklat'], 'string'],
            [['pendaftaran_id', 'pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id', 'pegawai_id', 'asalrujukan_id', 'ruanganasal_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'pasien_id', 'pasienadmisi_id', 'ruangan_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id', 'pegawai_id', 'asalrujukan_id', 'ruanganasal_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'pasien_id', 'pasienadmisi_id', 'ruangan_id'], 'integer'],
            [['tglmasukpenunjang', 'tanggal_lahir', 'tgl_rujukan'], 'safe'],
            [['is_bayar'], 'boolean'],
            [['no_pendaftaran', 'no_masukpenunjang', 'jeniskelamin'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_penunjang', 'status_periksa', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['no_antrian', 'status_penunjang'], 'string', 'max' => 100],
            [['umur'], 'string', 'max' => 30],
            [['j_kelamin'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe_pasien' => 'Tipe Pasien',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'pegawai_id' => 'Pegawai ID',
            'dokter_penunjang' => 'Dokter Penunjang',
            'no_rujukan' => 'No Rujukan',
            'asalrujukan_id' => 'Asalrujukan ID',
            'asalrujukan_nama' => 'Asalrujukan Nama',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangan_nama' => 'Ruangan Nama',
            'status_periksa' => 'Status Periksa',
            'no_antrian' => 'No Antrian',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'umur' => 'Umur',
            'jeniskelamin' => 'Jeniskelamin',
            'j_kelamin' => 'J Kelamin',
            'tanggal_lahir' => 'Tanggal Lahir',
            'kuning' => 'Kuning',
            'merah' => 'Merah',
            'ungu' => 'Ungu',
            'coklat' => 'Coklat',
            'tgl_rujukan' => 'Tgl Rujukan',
            'pasien_id' => 'Pasien ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'is_bayar' => 'Is Bayar',
            'status_penunjang' => 'Status Penunjang',
        ];
    }

    public static function primaryKey()
    {
        return ["no_rekam_medik"];
    }
}
