<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-19 14:34:01
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-01 18:43:47
 */

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "reseptur_t".
 *
 * @property int $reseptur_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pegawai_id
 * @property int $pendaftaran_id
 * @property int $penjualanresep_id
 * @property string $tglreseptur
 * @property string $noresep
 * @property int $ruanganreseptur_id
 * @property int $status_reseptur lookup_type='status_reseptur'
 * @property int $antrian_id
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
 * @property int $instruksi_id
 * @property bool $is_hamil
 * @property int $berat_badan
 * @property int $tinggi_badan
 * @property string $luas_tubuh
 * @property int $diagnosa_id
 *
 * @property PenjualanresepT[] $penjualanresepTs
 * @property ResepturdetailT[] $resepturdetailTs
 */
class ResepturForm extends \yii\base\Model
{
	// Public variable
	public $reseptur_id;
	public $pasienadmisi_id;
	public $ruangan_id;
	public $pasien_id;
	public $pegawai_id;
	public $pendaftaran_id;
	public $penjualanresep_id;
	public $tglreseptur;
	public $noresep;
	public $ruanganreseptur_id;
	public $status_reseptur;
	public $antrian_id;
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
	public $instruksi_id;
	public $is_hamil;
	public $berat_badan;
	public $tinggi_badan;
	public $luas_tubuh;
	public $diagnosa_id;
	public $iter;
	public $racikan_id;
	public $depo_id;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'reseptur_t';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instruksi_id', 'diagnosa_id', 'depo_id'], 'default', 'value' => null],
			[['reseptur_id', 'pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instruksi_id', 'diagnosa_id'], 'integer'],
			[['ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'tglreseptur', 'ruanganreseptur_id'], 'required'],
			[['tglreseptur', 'created_date', 'last_modified_date', 'deleted_date', 'berat_badan', 'tinggi_badan', 'iter'], 'safe'],
			[['additional_data'], 'string'],
			[['is_deleted', 'is_active', 'is_hamil'], 'boolean'],
			[['noresep'], 'string', 'max' => 50],
			[['luas_tubuh'], 'string', 'max' => 100],
			[['noresep'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pasienadmisi_id' => 'Pasienadmisi ID',
			'ruangan_id' => 'Ruangan ID',
			'pasien_id' => 'Pasien ID',
			'pegawai_id' => 'Pegawai ID',
			'pendaftaran_id' => 'Pendaftaran ID',
			'penjualanresep_id' => 'Penjualanresep ID',
			'tglreseptur' => 'Tglreseptur',
			'noresep' => 'Noresep',
			'ruanganreseptur_id' => 'Ruanganreseptur ID',
			'status_reseptur' => 'Status Reseptur',
			'antrian_id' => 'Antrian ID',
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
			'instruksi_id' => 'Instruksi ID',
			'is_hamil' => 'Is Hamil',
			'berat_badan' => 'Berat Badan',
			'tinggi_badan' => 'Tinggi Badan',
			'luas_tubuh' => 'Luas Tubuh',
			'diagnosa_id' => 'Diagnosa',
		];
	}
}
?>