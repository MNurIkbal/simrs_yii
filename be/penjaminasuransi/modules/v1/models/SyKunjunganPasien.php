<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sy_kunjungan".
 *
* @property string kunjungan_id
* @property string no_pendaftaran
* @property string no_rekammedik
* @property string nama_pasien
* @property string jenis_kelamin
* @property string tgl_lahir
* @property string umur
* @property string tgl_pendaftaran
* @property string tgl_pulang
* @property string instalasi_kode
* @property string instalasi_id
* @property string instalasi_nama
* @property string ruangan_kode
* @property string ruangan_id
* @property string ruangan_nama
* @property string carabayar_kode
* @property string carabayar_nama
* @property string penjamin_kode
* @property string penjamin_nama
* @property string kelas_kode
* @property string kelas_nama
* @property string dokter_kode
* @property string dokter_nama
* @property string kasus_penyakit
* @property string no_sep
* @property string status_kunjungan
* @property string $additional_data
* @property string $info_response_bpjs
* @property string $created_date
* @property integer $created_by
* @property integer $modified_count
* @property string $last_modified_date
* @property integer $last_modified_by
* @property boolean $is_deleted
* @property boolean $is_active
* @property string $deleted_date
* @property integer $deleted_by
* @property boolean $is_verifikasi
* @property boolean $total_verifikasi
* @property int $identitas_id
* @property string $identitas_nama
* @property string $identitas_value
* @property string $no_klaimcovid
 */
class SyKunjunganPasien extends \Doco\components\DocoActiveRecord
{
    /* INFO Jika di uncomment maka menyebabkan validasi sep erros*/
    // public $kunjungan_id;
    // public $no_pendaftaran;
    // public $no_rekammedik;
    // public $nama_pasien;
    // public $jenis_kelamin;
    // public $tgl_lahir;
    // public $umur;
    // public $tgl_pendaftaran;
    // public $tgl_pulang;
    // public $instalasi_kode;
    // public $instalasi_nama;
    // public $ruangan_kode;
    // public $ruangan_nama;
    // public $carabayar_kode;
    // public $carabayar_nama;
    // public $penjamin_kode;
    // public $penjamin_nama;
    // public $kelas_kode;
    // public $kelas_nama;
    // public $dokter_kode;
    // public $dokter_nama;
    // public $kasus_penyakit;
    // public $no_sep;
    // public $status_kunjungan;
    // /**
    //  * @inheritdoc
    //  */
    public static function tableName()
    {
        return 'sy_kunjungan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id','ruangan_id','tgl_lahir', 'tgl_pendaftaran', 'tgl_pulang', 'created_date', 'last_modified_date', 'deleted_date', 'is_verifikasi', 'identitas_id', 'identitas_nama', 'identitas_value', 'no_klaimcovid','jeniskasuspenyakit_id','jeniskasuspenyakit_nama', 'no_pembayaran', 'nosep', 'pasien_id','info_response_bpjs','additional_data', 'status_idrg', 'status_inacbg'], 'safe'],
            [['status_kunjungan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'hak_kelasbpjs', 'total_verifikasi'], 'default', 'value' => null],
            [['status_kunjungan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'hak_kelasbpjs'], 'integer'],
            [['additional_data','info_response_bpjs'], 'string'],
            [['is_deleted', 'is_active' , 'is_verifikasi'], 'boolean'],
            [['no_pendaftaran', 'no_rekammedik', 'no_sep', 'no_asuransi'], 'string', 'max' => 150],
            [['nama_pasien','jeniskasuspenyakit_nama'], 'string', 'max' => 255],
            [['jenis_kelamin', 'umur', 'instalasi_nama', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'kelas_nama', 'dokter_nama', 'no_kamar', 'no_tempattidur'], 'string', 'max' => 100],
            [['instalasi_kode', 'ruangan_kode', 'carabayar_kode', 'penjamin_kode', 'kelas_kode', 'dokter_kode', 'carakeluar_kode', 'lama_rawat', 'no_pendaftaran'], 'string', 'max' => 50],
        ];
    }
}
