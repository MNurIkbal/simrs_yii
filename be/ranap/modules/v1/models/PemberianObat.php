<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:15:16
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-31 10:05:59
 */

// Namespace
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemberianobat_t".
 *
 * @property int $pemberianobat_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pemberianobat
 * @property string $catatan_pemberian
 * @property int $dokterdpjp_id
 * @property bool $is_reseptur
 * @property bool $is_hamil
 * @property bool $is_alergi
 * @property int $berat_badan
 * @property int $tinggi_badan
 * @property string $luas_tubuh
 * @property int $diagnosa_id
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
class PemberianObat extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'pemberianobat_t';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pendaftaran_id', 'pasienadmisi_id'], 'required'],
			[['pendaftaran_id', 'pasienadmisi_id', 'dokterdpjp_id', 'berat_badan', 'tinggi_badan', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['pemberianobat_id', 'pendaftaran_id', 'pasienadmisi_id', 'dokterdpjp_id', 'berat_badan', 'tinggi_badan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['catatan_pemberian', 'additional_data'], 'string'],
			[['is_reseptur', 'is_hamil', 'is_alergi', 'is_deleted', 'is_active'], 'boolean'],
			[['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['no_pemberianobat'], 'string', 'max' => 255],
			[['luas_tubuh'], 'string', 'max' => 100],
			[['pemberianobat_id'], 'unique'],
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
			'pasienadmisi_id' => 'Pasienadmisi ID',
			'no_pemberianobat' => 'No Pemberianobat',
			'catatan_pemberian' => 'Catatan Pemberian',
			'dokterdpjp_id' => 'Dokterdpjp ID',
			'is_reseptur' => 'Is Reseptur',
			'is_hamil' => 'Is Hamil',
			'is_alergi' => 'Is Alergi',
			'berat_badan' => 'Berat Badan',
			'tinggi_badan' => 'Tinggi Badan',
			'luas_tubuh' => 'Luas Tubuh',
			'diagnosa_id' => 'Diagnosa ID',
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