<?php
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "diagnosa_v".
 *
 * @property int $kamartempattidur_id
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property int $kamarruangan_jenis
 * @property int $kelaspelayanan_id
 * @property int $kettempattidur_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $kelaspelayanan_nama
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 * @property boolean $status_isi
 */
class DashboardKamarView extends \Doco\components\DocoActiveRecord //\yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'dashboardkamar_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['kamartempattidur_id', 'kamarruangan_id', 'ruangan_id', 'ruangan_nama', 'kamarruangan_nokamar', 'kamarruangan_jenis', 'no_tempattidur', 'kelaspelayanan_id', 'kelaspelayanan_nama', 'status_isi', 'kettempattidur_id', 'kettempattidur_nama', 'kettempattidur_warna', 'kode_warna'], 'default', 'value' => null],
			[['kamartempattidur_id', 'kamarruangan_id', 'ruangan_id', 'kamarruangan_jenis', 'kelaspelayanan_id', 'kettempattidur_id'], 'integer'],
			[['ruangan_nama', 'kamarruangan_nokamar', 'no_tempattidur', 'kelaspelayanan_nama', 'kettempattidur_nama', 'kettempattidur_warna', 'kode_warna'], 'string'],
			[['ruangan_nama', 'kamarruangan_nokamar', 'kelaspelayanan_nama'], 'string', 'max' => 200],
			[['no_tempattidur', 'kettempattidur_warna'], 'string', 'max' => 100],
			[['kode_warna', 'kettempattidur_nama'], 'string', 'max' => 50],
			[['status_isi'], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'kamartempattidur_id' => 'Kamar Tempat Tidur ID',
			'kamarruangan_id' => 'Kamar Ruangan ID',
			'ruangan_id' => 'Ruangan ID',
			'ruangan_nama' => 'Nama Ruangan',
			'kamarruangan_nokamar' => 'No Kamar Ruangan',
			'kamarruangan_jenis' => 'Jenis Kamar Ruangan',
			'no_tempattidur' => 'No Tempat Tidur',
			'kelaspelayanan_id' => 'Kelas Pelayanan ID',
			'kelaspelayanan_nama' => 'Nama Kelas Pelayanan',
			'status_isi' => 'Status Isi',
			'kettempattidur_id' => 'Keterangan Tempat Tidur ID',
			'kettempattidur_nama' => 'Nama Keterangan Tempat Tidur',
			'kettempattidur_warna' => ' Warna Keterangan Tempat Tidur',
			'kode_warna' => 'Kode Warna',
		];
	}
}
