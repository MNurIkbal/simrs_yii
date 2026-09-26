<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 14:16:13
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-05 13:13:51
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompokpemeriksaanrad_m".
 *
 * @property int $kelompokpemeriksaanRad_id
 * @property string $kode_kelompok
 * @property string $nama_kelompok
 * @property string $keterangan_kelompok
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
class KelompokPemeriksaanRadiologiForm extends \yii\base\Model
{
	// Public property
	public $kelompokpemeriksaanrad_id;
	public $kode_kelompok;
	public $nama_kelompok;
	public $keterangan_kelompok;
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
	 * {@inheritdoc}
	 */
	// public static function tableName()
	// {
	// 	return 'kelompokpemeriksaanrad_m';
	// }

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['kode_kelompok', 'nama_kelompok'], 'required'],
            [['nama_kelompok'], 'checkUnique'],
			[['additional_data'], 'string'],
			[['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['kode_kelompok'], 'string', 'max' => 25],
			[['nama_kelompok', 'keterangan_kelompok'], 'string', 'max' => 255],
		];
	}


	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'kelompokpemeriksaanrad_id' => 'Kelompokpemeriksaanrad ID',
			'kode_kelompok' => 'Kode Kelompok',
			'nama_kelompok' => 'Kelompok Pemeriksaan',
			'keterangan_kelompok' => 'Keterangan Kelompok',
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

	public function checkUnique()
	{
		$nama_kelompok = $this->nama_kelompok;
        if (strpos(substr($nama_kelompok, 0, 1), ' ') !== FALSE) {
            $this->addError('nama_kelompok', 'Kelompok pemeriksaan mengandung spasi di awal kata');
            return false;
        }
	}
}
?>
