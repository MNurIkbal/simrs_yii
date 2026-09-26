<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporankematian_v".
 *
 * @property string $tglpasienpulang
 * @property string $tgl_masukpasien
 * @property string $nama_pasien
 * @property string $no_identitas_pasien
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $pendidikan_nama
 * @property string $pekerjaan_nama
 * @property string $status_kependudukan
 * @property string $alamat_pasien
 * @property string $kelurahan_nama
 * @property string $kecamatan_nama
 * @property string $kabupaten_nama
 * @property string $tgl_meninggal
 * @property string $umur_meninggal
 * @property string $lahir_mati
 * @property string $alm_dlm_keadaan
 * @property string $tempat_meninggal
 * @property string $kondisikeluar_nama
 * @property string $sumber_data
 * @property string $rencana_pemulasaran
 * @property string $tempat_pemulasaran
 * @property string $dokter_menerangkan
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $diagnosa_utama
 * @property int $diagnosa_utama_id
 * @property string $diagnosa_penyerta
 * @property int $diagnosa_penyerta_id
 */
class LaporankematianV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporankematian_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tglpasienpulang', 'tgl_masukpasien', 'tanggal_lahir', 'tgl_meninggal'], 'safe'],
            [['no_identitas_pasien', 'jeniskelamin', 'status_kependudukan', 'alamat_pasien', 'umur_meninggal', 'lahir_mati', 'alm_dlm_keadaan', 'tempat_meninggal', 'sumber_data', 'rencana_pemulasaran', 'tempat_pemulasaran', 'diagnosa_utama', 'diagnosa_penyerta'], 'string'],
            [['ruangan_id', 'instalasi_id', 'carabayar_id', 'penjamin_id', 'diagnosa_utama_id', 'diagnosa_penyerta_id'], 'default', 'value' => null],
            [['ruangan_id', 'instalasi_id', 'carabayar_id', 'penjamin_id', 'diagnosa_utama_id', 'diagnosa_penyerta_id'], 'integer'],
            [['nama_pasien', 'pendidikan_nama', 'pekerjaan_nama', 'kelurahan_nama', 'kecamatan_nama', 'kabupaten_nama', 'dokter_menerangkan', 'ruangan_nama', 'instalasi_nama', 'carabayar_nama'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['kondisikeluar_nama'], 'string', 'max' => 100],
            [['penjamin_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tglpasienpulang' => 'Tglpasienpulang',
            'tgl_masukpasien' => 'Tgl Masukpasien',
            'nama_pasien' => 'Nama Pasien',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'pendidikan_nama' => 'Pendidikan Nama',
            'pekerjaan_nama' => 'Pekerjaan Nama',
            'status_kependudukan' => 'Status Kependudukan',
            'alamat_pasien' => 'Alamat Pasien',
            'kelurahan_nama' => 'Kelurahan Nama',
            'kecamatan_nama' => 'Kecamatan Nama',
            'kabupaten_nama' => 'Kabupaten Nama',
            'tgl_meninggal' => 'Tgl Meninggal',
            'umur_meninggal' => 'Umur Meninggal',
            'lahir_mati' => 'Lahir Mati',
            'alm_dlm_keadaan' => 'Alm Dlm Keadaan',
            'tempat_meninggal' => 'Tempat Meninggal',
            'kondisikeluar_nama' => 'Kondisikeluar Nama',
            'sumber_data' => 'Sumber Data',
            'rencana_pemulasaran' => 'Rencana Pemulasaran',
            'tempat_pemulasaran' => 'Tempat Pemulasaran',
            'dokter_menerangkan' => 'Dokter Menerangkan',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'diagnosa_utama' => 'Diagnosa Utama',
            'diagnosa_utama_id' => 'Diagnosa Utama ID',
            'diagnosa_penyerta' => 'Diagnosa Penyerta',
            'diagnosa_penyerta_id' => 'Diagnosa Penyerta ID',
        ];
    }
}
