<?php

namespace app\modules\rm\models;

use Yii;

/**
 * This is the model class for table "pengirimanrm_t".
 *
 * @property int $pengirimanrm_id
 * @property int $peminjamanrm_id
 * @property int $kembalirm_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $dokrekammedis_id
 * @property int $ruangan_id
 * @property int $petugaspengirim_id
 * @property int $ruanganpengirim_id
 * @property int $petugaspenerima_id
 * @property int $ruanganpenerima_id
 * @property string $nourut_keluar
 * @property string $tgl_pengirimanrm
 * @property bool $kelengkapan_dokumen
 * @property bool $print_pengiriman
 * @property string $tgl_terimadokrm
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
 *
 * @property PendaftaranT[] $pendaftaranTs
 * @property DokrekammedisM $dokrekammedis
 * @property KembalirmT $kembalirm
 * @property PasienM $pasien
 * @property PegawaiM $petugaspengirim
 * @property PegawaiM $petugaspenerima
 * @property PeminjamanrmT $peminjamanrm
 * @property PendaftaranT $pendaftaran
 * @property RuanganM $ruangan
 * @property RuanganM $ruanganpengirim
 * @property RuanganM $ruanganpenerima
 */
class PengirimanRmForm extends \yii\base\Model
{
    public $peminjamanrm_t;
    public $tglpeminjamanrm;
    public $pasien_m;
    public $idx_pasien;
    public $idx_instalasi;
    public $idx_ruangan;
    public $keterangan_peminjaman;
    public $idx_pegawai;
    public $instalasi_id;
    public $pegawai_id;
    public $peminjamanrm_id;
    public $kembalirm_id;
    public $pasien_id;
    public $pendaftaran_id;
    public $dokrekammedis_id;
    public $ruangan_id;
    public $petugaspengirim_id;
    public $ruanganpengirim_id;
    public $petugaspenerima_id;
    public $ruanganpenerima_id;
    public $nourut_keluar;
    public $tgl_pengirimanrm;
    public $kelengkapan_dokumen;
    public $print_pengiriman;
    public $tgl_terimadokrm;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pengirimanrm_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['peminjamanrm_id', 'kembalirm_id', 'pasien_id', 'pendaftaran_id', 'dokrekammedis_id', 'ruangan_id', 'petugaspengirim_id', 'ruanganpengirim_id', 'petugaspenerima_id', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['peminjamanrm_id', 'kembalirm_id', 'pasien_id', 'pendaftaran_id', 'dokrekammedis_id', 'ruangan_id', 'petugaspengirim_id', 'ruanganpengirim_id', 'petugaspenerima_id', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['idx_pasien', 'idx_instalasi', 'idx_ruangan','idx_pegawai', 'tgl_pengirimanrm'], 'required'],
            [['tgl_pengirimanrm', 'tgl_terimadokrm', 'created_date', 'last_modified_date', 'deleted_date','peminjamanrm_t','tglpeminjamanrm','pasien_m','pegawai_id','instalasi_id'], 'safe'],
            [['kelengkapan_dokumen', 'print_pengiriman', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['nourut_keluar'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pengirimanrm_id' => 'Pengirimanrm ID',
            'peminjamanrm_id' => 'Peminjamanrm ID',
            'kembalirm_id' => 'Kembalirm ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'ruangan_id' => 'Ruangan ID',
            'petugaspengirim_id' => 'Petugaspengirim ID',
            'ruanganpengirim_id' => 'Ruanganpengirim ID',
            'petugaspenerima_id' => 'Petugaspenerima ID',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'nourut_keluar' => 'Nourut Keluar',
            'tgl_pengirimanrm' => 'Tgl Pengirimanrm',
            'kelengkapan_dokumen' => 'Kelengkapan Dokumen',
            'print_pengiriman' => 'Print Pengiriman',
            'tgl_terimadokrm' => 'Tgl Terimadokrm',
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
            'tglpeminjamanrm' => 'Tanggal Peminjaman',
            'idx_pasien' => 'No Rekam Medik',
            'idx_instalasi' => 'Instalasi Tujuan',
            'idx_ruangan' => 'Ruangan Tujuan',
            'idx_pegawai' => 'Nama Peminjam Dokumen'
        ];
    }
}
