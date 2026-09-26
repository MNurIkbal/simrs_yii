<?php
/**
 * @Author: Sigit
 * @Date:   2018-08-13 14:28:58
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-08-14 10:14:38
 */

namespace app\modules\v1\models;

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
 * @property int $kamarruangan_id
 * @property int $kamartempattidur_id
 * @property bool $is_active
 */
class Cppt extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'cppt_t';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			// [['pendaftaran_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama'], 'required', 'on' => 'soap'],
			[['pendaftaran_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id'], 'required', 'on' => 'soap'],
			[['pasien_id', 'ruangan_id', 'pegawai_id', 'ruangan_id', 'instruksi', 'pemberi_instruksi_id'], 'required', 'on' => 'verbalorder'],	
			[['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'a_diag_utama', 'a_diag_penyerta', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id', 'kamarruangan_id', 'kamartempattidur_id'], 'default', 'value' => null],
			[['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id', 'kamarruangan_id', 'kamartempattidur_id'], 'integer'],
			[['tgl_cppt', 'tgl_verifikasi', 'is_active'], 'safe'],
			[['subject', 'object', 'planning', 'catatan_dokter', 'catatan_perawat', 'instruksi'], 'string'],
			[['is_instruksi_pulang', 'is_verifikasi', 'is_active'], 'boolean'],
			[['kamar_tempattidur'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'cppt_id' => 'Cppt ID',
			'pendaftaran_id' => 'Pendaftaran ID',
			'pasienadmisi_id' => 'Pasienadmisi ID',
			'pasien_id' => 'Pasien ID',
			'ruangan_id' => 'Ruangan ID',
			'kamar_tempattidur' => 'Kamar Tempattidur',
			'tgl_cppt' => 'Tgl Cppt',
			'subject' => 'Subject',
			'object' => 'Object',
			'a_diag_utama' => 'A Diag Utama',
			'a_diag_penyerta' => 'A Diag Penyerta',
			'planning' => 'Planning',
			'is_instruksi_pulang' => 'Is Instruksi Pulang',
			'pegawai_id' => 'Pegawai ID',
			'catatan_dokter' => 'Catatan Dokter',
			'catatan_perawat' => 'Catatan Perawat',
			'instruksi' => 'Instruksi',
			'pemberi_instruksi_id' => 'Pemberi Instruksi ID',
			'is_verifikasi' => 'Is Verifikasi',
			'pegawai_verifikasi_id' => 'Pegawai Verifikasi ID',
			'tgl_verifikasi' => 'Tgl Verifikasi',
			'kamarruangan_id' => 'Kamarruangan ID',
			'kamartempattidur_id' => 'Kamar tempat tidur ID',
		];
	}
}