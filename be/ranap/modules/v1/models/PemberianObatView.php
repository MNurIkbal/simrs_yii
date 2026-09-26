<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:18:51
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-24 16:25:11
 */

// Namespace
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemberianobat_v".
 *
 * @property int $pemberianobat_id
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 * @property string $jenis_kelamin
 * @property string $umur
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $kelaspelayanan_nama
 * @property string $dokter_dpjp
 * @property string $penjamin_nama
 * @property int $berat_badan
 * @property int $tinggi_badan
 * @property string $luas_tubuh
 * @property bool $is_hamil
 * @property bool $is_alergi
 * @property string $diagnosa_nama
 */
class PemberianObatView extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'pemberianobat_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pemberianobat_id', 'pendaftaran_id', 'pasien_id', 'berat_badan', 'tinggi_badan'], 'default', 'value' => null],
			[['pemberianobat_id', 'pendaftaran_id', 'pasien_id', 'berat_badan', 'tinggi_badan'], 'integer'],
			[['tanggal_lahir'], 'safe'],
			[['is_hamil', 'is_alergi'], 'boolean'],
			[['no_pendaftaran'], 'string', 'max' => 20],
			[['no_rekam_medik'], 'string', 'max' => 10],
			[['nama_pasien', 'ruangan_nama', 'kelaspelayanan_nama', 'dokter_dpjp', 'penjamin_nama'], 'string', 'max' => 50],
			[['jenis_kelamin', 'diagnosa_nama'], 'string', 'max' => 200],
			[['umur'], 'string', 'max' => 30],
			[['kamarruangan_nokamar'], 'string', 'max' => 25],
			[['no_tempattidur'], 'string', 'max' => 255],
			[['luas_tubuh'], 'string', 'max' => 100],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pemberianobat_id' => 'Pemberianobat ID',
			'pendaftaran_id' => 'Pendaftaran ID',
			'no_pendaftaran' => 'No Pendaftaran',
			'pasien_id' => 'Pasien ID',
			'no_rekam_medik' => 'No Rekam Medik',
			'nama_pasien' => 'Nama Pasien',
			'tanggal_lahir' => 'Tanggal Lahir',
			'jenis_kelamin' => 'Jenis Kelamin',
			'umur' => 'Umur',
			'ruangan_nama' => 'Ruangan Nama',
			'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
			'no_tempattidur' => 'No Tempattidur',
			'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
			'dokter_dpjp' => 'Dokter Dpjp',
			'penjamin_nama' => 'Penjamin Nama',
			'berat_badan' => 'Berat Badan',
			'tinggi_badan' => 'Tinggi Badan',
			'luas_tubuh' => 'Luas Tubuh',
			'is_hamil' => 'Is Hamil',
			'is_alergi' => 'Is Alergi',
			'diagnosa_nama' => 'Diagnosa Nama',
		];
	}
}
?>