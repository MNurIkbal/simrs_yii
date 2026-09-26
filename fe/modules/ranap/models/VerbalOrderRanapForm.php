<?php

/**
 * @Author: Iqbal
 * @Date:   2018-07-05 13:43:05
 * @Last Modified by:   Iqbal
 * @Last Modified time: 2018-07-09 17:16:18
 */

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "cppt_t".
 *
 * @property int $cppt_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $ruangan_id
 * @property string $kamar_tempattidur formatnya : [no_kamar]-[no_tempattidur]
 * @property string $tgl_cppt
 * @property string $subject
 * @property string $object
 * @property int $a_diag_utama
 * @property string $a_diag_penyerta
 * @property string $planning
 * @property bool $is_instruksi_pulang
 * @property int $pegawai_id
 * @property string $catatan_dokter
 * @property string $catatan_perawat
 * @property string $instruksi
 * @property int $pemberi_instruksi_id
 * @property bool $is_verifikasi
 * @property int $pegawai_verifikasi_id
 * @property string $tgl_verifikasi
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
class VerbalOrderRanapForm extends \yii\base\Model
{
	/**
	 * {@inheritdoc}
	 */
	public $cppt_id;
	public $pendaftaran_id;
	public $pasienadmisi_id;
	public $pasien_id;
	public $ruangan_id;
	public $tgl_cppt;
	public $pegawai_id;
	public $subject;
	public $object;
	public $a_diag_utama;
	public $a_diag_penyerta;
	public $planning;
	public $catatan_dokter;
	public $catatan_perawat;
	public $instruksi;
	public $kamar_tempattidur;
	public $pemberi_instruksi_id;
	public $kamarruangan_id;
	public $kamartempattidur_id;
	public $last_modified_by;
	public $deleted_by;
	public $pegawai_verifikasi_id;
	public $additional_data;
	public $is_instruksi_pulang;
	public $is_verifikasi;
	public $is_deleted;
	public $is_active;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pasien_id', 'ruangan_id', 'pegawai_id', 'ruangan_id', 'instruksi', 'pemberi_instruksi_id',], 'required'],			
			[['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'a_diag_utama', 'pegawai_id', 'pemberi_instruksi_id', 'last_modified_by', 'deleted_by', 'instruksi','kamarruangan_id','kamartempattidur_id'], 'default', 'value' => null],
			[['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'a_diag_utama', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id','kamarruangan_id','kamartempattidur_id'], 'integer'],
			[['tgl_cppt', 'tgl_verifikasi', 'created_date', 'last_modified_date', 'deleted_date','created_by'], 'safe'],
			[['subject', 'object', 'planning', 'catatan_dokter', 'catatan_perawat', 'instruksi', 'additional_data','instruksi'], 'string'],
			[['is_instruksi_pulang', 'is_verifikasi', 'is_deleted', 'is_active'], 'boolean'],
			['a_diag_penyerta', 'each', 'rule' => ['integer']],
			[['kamar_tempattidur'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'cppt_id' => 'Cppt',
			'pendaftaran_id' => 'Pendaftaran',
			'pasienadmisi_id' => 'Pasien Admisi',
			'pasien_id' => 'Pasien',
			'ruangan_id' => 'Ruangan',
			'kamar_tempattidur' => 'Kamar Tempat Tidur',
			'tgl_cppt' => 'Tanggal Cppt',
			'subject' => 'Subject',
			'object' => 'Object',
			'a_diag_utama' => 'Diagnosa Utama',
			'a_diag_penyerta' => 'Diagnosa Penyerta',
			'planning' => 'Planning',
			'is_instruksi_pulang' => 'Is Instruksi Pulang',
			'pegawai_id' => 'Pegawai ID',
			'catatan_dokter' => 'Catatan Dokter',
			'catatan_perawat' => 'Catatan Perawat',
			'instruksi' => 'Instruksi',
			'pemberi_instruksi_id' => 'Pemberi Instruksi',
			'is_verifikasi' => 'Is Verifikasi',
			'pegawai_verifikasi_id' => 'Pegawai Verifikasi ID',
			'tgl_verifikasi' => 'Tgl Verifikasi',
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
?>