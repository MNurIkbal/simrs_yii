<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-12 16:30:29
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-12 16:36:11
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "informasipengirimanrm_v".
 *
 * @property int $pengirimanrm_id
 * @property int $peminjamanrm_id
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property int $pendaftaran_id
 * @property int $dokrekammedis_id
 * @property int $ruangantujuan_id
 * @property string $ruangantujuan_nama
 * @property int $instalasitujuan_id
 * @property string $instalasitujuan_nama
 * @property string $nourut_keluar
 * @property string $tgl_pengirimanrm
 * @property bool $kelengkapan_dokumen
 * @property int $petugaspengirim_id
 * @property bool $print_pengiriman
 * @property int $ruanganpengirim_id
 * @property string $created_date
 * @property string $last_modified_date
 * @property int $created_by
 * @property int $last_modified_by
 * @property int $kembalirm_id
 * @property string $tglkembali
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $statusdok_rekammedik
 * @property bool $is_deleted
 */
class InformasiPengirimanRmView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informasipengirimanrm_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pengirimanrm_id', 'peminjamanrm_id', 'pasien_id', 'pendaftaran_id', 'dokrekammedis_id', 'ruangantujuan_id', 'instalasitujuan_id', 'petugaspengirim_id', 'ruanganpengirim_id', 'created_by', 'last_modified_by', 'kembalirm_id'], 'default', 'value' => null],
            [['pengirimanrm_id', 'peminjamanrm_id', 'pasien_id', 'pendaftaran_id', 'dokrekammedis_id', 'ruangantujuan_id', 'instalasitujuan_id', 'petugaspengirim_id', 'ruanganpengirim_id', 'created_by', 'last_modified_by', 'kembalirm_id'], 'integer'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_pengirimanrm', 'created_date', 'last_modified_date', 'tglkembali', 'tgl_pendaftaran'], 'safe'],
            [['alamat_pasien'], 'string'],
            [['kelengkapan_dokumen', 'print_pengiriman', 'is_deleted'], 'boolean'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['namadepan', 'jeniskelamin', 'no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'ruangantujuan_nama', 'instalasitujuan_nama', 'statusdok_rekammedik'], 'string', 'max' => 50],
            [['nama_bin'], 'string', 'max' => 30],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['nourut_keluar'], 'string', 'max' => 5],
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
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'ruangantujuan_nama' => 'Ruangantujuan Nama',
            'instalasitujuan_id' => 'Instalasitujuan ID',
            'instalasitujuan_nama' => 'Instalasitujuan Nama',
            'nourut_keluar' => 'Nourut Keluar',
            'tgl_pengirimanrm' => 'Tgl Pengirimanrm',
            'kelengkapan_dokumen' => 'Kelengkapan Dokumen',
            'petugaspengirim_id' => 'Petugaspengirim ID',
            'print_pengiriman' => 'Print Pengiriman',
            'ruanganpengirim_id' => 'Ruanganpengirim ID',
            'created_date' => 'Created Date',
            'last_modified_date' => 'Last Modified Date',
            'created_by' => 'Created By',
            'last_modified_by' => 'Last Modified By',
            'kembalirm_id' => 'Kembalirm ID',
            'tglkembali' => 'Tglkembali',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'statusdok_rekammedik' => 'Statusdok Rekammedik',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
