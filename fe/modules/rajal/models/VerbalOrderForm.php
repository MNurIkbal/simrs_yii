<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\rajal\models;

use Yii;

/**
 *
 * @property int $pendaftaran_id
 * @property int $pegawai_id
 * @property int $pasien_id
 * @property int $ruangan_id
 * @property string $instruksi
 * @property string $ruangan_nama
 * @property int $pemberi_instruksi_id
 * @property int $fee_konsul
 * @property int $kelaspelayanan_id
 * @property int $penjamin_id
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
class VerbalOrderForm extends \yii\base\Model
{
	/**
	 * {@inheritdoc}
	 */
	public $pendaftaran_id;
	public $pegawai_id;
	public $pasien_id;
	public $ruangan_id;
	public $instruksi;
	public $ruangan_nama;
	public $pemberi_instruksi_id;
	public $fee_konsul;
	public $kelaspelayanan_id;
	public $penjamin_id;
	public $created_by;
	public $created_date;
	public $last_modified_by;
	public $deleted_by;
	public $modified_count;
	public $additional_data;
	public $last_modified_date;
	public $deleted_date;
	public $is_deleted;
	public $is_active;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pendaftaran_id', 'instruksi', 'pemberi_instruksi_id', 'fee_konsul'], 'required'],			
			[['pendaftaran_id', 'ruangan_id', 'pemberi_instruksi_id', 'last_modified_by', 'deleted_by', 'instruksi'], 'default', 'value' => null],
			[['ruangan_id', 'pemberi_instruksi_id', 'fee_konsul'], 'integer'],
			[['created_date', 'last_modified_date', 'deleted_date','created_by', 'penjamin_id', 'kelaspelayanan_id'], 'safe'],
			[['pendaftaran_id','instruksi', 'additional_data'], 'string'],
			[['is_deleted', 'is_active'], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pendaftaran_id' => 'Pendaftaran ID',
			'pegawai_id' => 'Pegawai ID',
			'pasien_id' => 'Pasien ID',
			'ruangan_id' => 'Ruangan',
			'instruksi' => 'Instruksi',
			'ruangan_nama' => 'Ruangan',
			'pemberi_instruksi_id' => 'Pemberi Instruksi',
			'fee_konsul' => 'Fee Konsul',
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