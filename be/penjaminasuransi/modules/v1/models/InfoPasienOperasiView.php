<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienoperasi_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property int $rencanaoperasi_id
 * @property string $tgl_rujukan
 * @property string $no_masukpenunjang
 * @property string $tgl_operasi
 * @property string $tglmasukpenunjang
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $pegawai_id
 * @property string $dokter_penunjang
 * @property string $no_rujukan
 * @property int $instalasiasal_id
 * @property string $asalrujukan_nama
 * @property int $ruanganasal_id
 * @property string $ruangan_nama
 * @property string $status_periksa
 * @property string $status
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
 * @property int $pasien_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property bool $is_bayar
 * @property string $status_penunjang
 */
class InfoPasienOperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienoperasi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id', 'rencanaoperasi_id', 'pegawai_id', 'instalasiasal_id', 'ruanganasal_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'pasien_id', 'pasienadmisi_id', 'ruangan_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id', 'rencanaoperasi_id', 'pegawai_id', 'instalasiasal_id', 'ruanganasal_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'pasien_id', 'pasienadmisi_id', 'ruangan_id'], 'integer'],
            [['tgl_rujukan', 'tgl_operasi', 'tglmasukpenunjang', 'tanggal_lahir'], 'safe'],
            [['kuning', 'merah', 'ungu', 'coklat'], 'string'],
            [['is_bayar'], 'boolean'],
            [['no_masukpenunjang', 'no_pendaftaran', 'jeniskelamin'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_penunjang', 'asalrujukan_nama', 'ruangan_nama', 'status_periksa', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['no_rujukan', 'no_antrian', 'status_penunjang'], 'string', 'max' => 100],
            [['status', 'j_kelamin'], 'string', 'max' => 200],
            [['umur'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'rencanaoperasi_id' => 'Rencanaoperasi ID',
            'tgl_rujukan' => 'Tgl Rujukan',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'tgl_operasi' => 'Tgl Operasi',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'pegawai_id' => 'Pegawai ID',
            'dokter_penunjang' => 'Dokter Penunjang',
            'no_rujukan' => 'No Rujukan',
            'instalasiasal_id' => 'Instalasiasal ID',
            'asalrujukan_nama' => 'Asalrujukan Nama',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangan_nama' => 'Ruangan Nama',
            'status_periksa' => 'Status Periksa',
            'status' => 'Status',
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
            'pasien_id' => 'Pasien ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'is_bayar' => 'Is Bayar',
            'status_penunjang' => 'Status Penunjang',
        ];
    }
    public function getDetail()
    {
        return $this->hasMany(InfoPasienOperasiDetailView::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }
    public function getIntraPosisi()
    {
        return $this->hasOne(InpostOperasi::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }
    public function extraFields()
    {
        return [
            'infopasienoperasidetail_v' => function($item){
                return $item->infoPasienOperasiDetail;
            },
        ];
    }
}
