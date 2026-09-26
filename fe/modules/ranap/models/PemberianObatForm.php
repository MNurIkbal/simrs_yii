<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:59:31
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-24 18:20:40
 */

// Namespace
namespace app\modules\ranap\models;

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
class PemberianObatForm extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public $pemberianobat_id;
	public $pendaftaran_id;
	public $pasienadmisi_id;
	public $dokterdpjp_id;
	public $berat_badan;
	public $tinggi_badan;
	public $diagnosa_id;
	public $catatan_pemberian;
	public $is_reseptur;
	public $is_hamil;
	public $is_alergi;
	public $luas_tubuh;
	public $no_pemberianobat;
	public $created_by;
	public $modified_count;
	public $last_modified_by;
	public $deleted_by;
	public $additional_data;
	public $is_deleted;
	public $is_active;
	public $created_date;
	public $last_modified_date;
	public $deleted_date;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pendaftaran_id', 'pasienadmisi_id'], 'required'],
			[['pemberianobat_id', 'pendaftaran_id', 'pasienadmisi_id', 'dokterdpjp_id', 'berat_badan', 'tinggi_badan', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['pemberianobat_id', 'pendaftaran_id', 'pasienadmisi_id', 'dokterdpjp_id', 'berat_badan', 'tinggi_badan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['catatan_pemberian', 'additional_data'], 'string'],
			[['is_reseptur', 'is_hamil', 'is_alergi', 'is_deleted', 'is_active'], 'boolean'],
			[['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['no_pemberianobat'], 'string', 'max' => 255],
			[['luas_tubuh'], 'string', 'max' => 100],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pemberianobat_id' => Yii::t('fe', 'Pemberian Obat'),
			'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
			'pasienadmisi_id' => Yii::t('fe', 'Pasien Admisi'),
			'no_pemberianobat' => Yii::t('fe', 'No Pemberian Obat'),
			'catatan_pemberian' => Yii::t('fe', 'Catatan Pemberian'),
			'dokterdpjp_id' => Yii::t('fe', 'Dokter Dpjp'),
			'is_reseptur' => Yii::t('fe', 'Is Reseptur'),
			'is_hamil' => Yii::t('fe', 'Hamil'),
			'is_alergi' => Yii::t('fe', 'Alergi'),
			'berat_badan' => Yii::t('fe', 'Berat Badan'),
			'tinggi_badan' => Yii::t('fe', 'Tinggi Badan'),
			'luas_tubuh' => Yii::t('fe', 'Luas Permukaan Tubuh'),
			'diagnosa_id' => Yii::t('fe', 'Diagnosa'),
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