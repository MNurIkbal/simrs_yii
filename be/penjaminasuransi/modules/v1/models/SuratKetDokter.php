<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "suratketdokter_t".
 *
 * @property int $suratketdokter_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property bool $is_doc
 * @property string $upload_doc
 * @property string $tgl_gejala
 * @property string $tgl_konsul
 * @property string $gejala_penyakit free text
 * @property string $diag_utama
 * @property string $diag_tambahan
 * @property string $faktor_penyebab free text
 * @property string $tgl_diagnosa
 * @property string $terapi_tindakan ambil dari nama tindakan
 * @property int $jenis_operasi 0=elective, 1=emergency
 * @property int $dokbedah_id pegawai_m
 * @property string $hasil_penunjang
 * @property int $sebebdiagnosa_id sebabdiagnosa_m
 * @property bool $is_kecelakaaan
 * @property string $tgl_kecelakaan
 * @property string $sebab_kecelakaan
 * @property bool $is_diag_sama
 * @property string $tgl_diag_sama
 * @property string $diag_sama
 * @property string $nama_rs
 * @property string $nama_dokter_rs
 * @property bool $is_rujukan
 * @property string $dokter_rujukan
 * @property string $alamat
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
 */
class SuratKetDokter extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suratketdokter_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'gejala_penyakit',
                'diag_utama',
                'diag_tambahan',
                'faktor_penyebab',
                'tgl_gejala',
                'tgl_konsul'
            ],'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'jenis_operasi', 'dokbedah_id', 'sebebdiagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['suratketdokter_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'jenis_operasi', 'dokbedah_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_doc', 'is_kecelakaaan', 'is_diag_sama', 'is_rujukan', 'is_deleted', 'is_active'], 'boolean'],
            [['upload_doc', 'gejala_penyakit', 'diag_utama', 'diag_tambahan', 'faktor_penyebab', 'terapi_tindakan', 'hasil_penunjang', 'sebab_kecelakaan', 'diag_sama', 'alamat', 'additional_data'], 'string'],
            [['tgl_gejala', 'tgl_konsul', 'tgl_diagnosa', 'tgl_kecelakaan', 'tgl_diag_sama', 'created_date', 'last_modified_date', 'deleted_date','instalasi_id','is_konsultasi','tgl_konsultasi','diag_konsultasi','nam_rs_konsul','nama_dr_konsul'], 'safe'],
            [['nama_rs', 'nama_dokter_rs', 'dokter_rujukan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'suratketdokter_id' => 'Suratketdokter ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'is_doc' => 'Is Doc',
            'upload_doc' => 'Upload Doc',
            'tgl_gejala' => 'Tgl Gejala',
            'tgl_konsul' => 'Tgl Konsul',
            'gejala_penyakit' => 'Gejala Penyakit',
            'diag_utama' => 'Diag Utama',
            'diag_tambahan' => 'Diag Tambahan',
            'faktor_penyebab' => 'Faktor Penyebab',
            'tgl_diagnosa' => 'Tgl Diagnosa',
            'terapi_tindakan' => 'Terapi Tindakan',
            'jenis_operasi' => 'Jenis Operasi',
            'dokbedah_id' => 'Dokbedah ID',
            'hasil_penunjang' => 'Hasil Penunjang',
            'sebebdiagnosa_id' => 'Sebebdiagnosa ID',
            'is_kecelakaaan' => 'Is Kecelakaaan',
            'tgl_kecelakaan' => 'Tgl Kecelakaan',
            'sebab_kecelakaan' => 'Sebab Kecelakaan',
            'is_diag_sama' => 'Is Diag Sama',
            'tgl_diag_sama' => 'Tgl Diag Sama',
            'diag_sama' => 'Diag Sama',
            'nama_rs' => 'Nama Rs',
            'nama_dokter_rs' => 'Nama Dokter Rs',
            'is_rujukan' => 'Is Rujukan',
            'dokter_rujukan' => 'Dokter Rujukan',
            'alamat' => 'Alamat',
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
        ];
    }
}
