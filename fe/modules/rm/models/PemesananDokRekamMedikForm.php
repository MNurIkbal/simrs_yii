<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-16 11:21:41
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-17 17:14:42
 */

namespace app\modules\rm\models;

use Yii;

/**
 * This is the model class for table "pesandokrm_t".
 *
 * @property int $pesandokrm_id
 * @property string $no_pesandokrm
 * @property int $ruanganpemesan_id
 * @property int $ruangantujuan_id
 * @property string $tgl_pesandokrm
 * @property string $tgl_mintakirim
 * @property int $status_pesan lookup_type='status_pesan'
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
class PemesananDokRekamMedikForm extends \yii\db\ActiveRecord
{
	// Public properties
	public $pesandokrm_id;
	public $no_pesandokrm;
	public $ruanganpemesan_id;
	public $ruangantujuan_id;
	public $tgl_pesandokrm;
	public $tgl_mintakirim;
	public $status_pesan;
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
	public static function tableName()
	{
		return 'pesandokrm_t';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['no_pesandokrm', 'ruanganpemesan_id', 'ruangantujuan_id', 'tgl_pesandokrm'], 'required'],
			[['ruanganpemesan_id', 'ruangantujuan_id', 'status_pesan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['ruanganpemesan_id', 'ruangantujuan_id', 'status_pesan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['tgl_pesandokrm', 'tgl_mintakirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['additional_data'], 'string'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['no_pesandokrm'], 'string', 'max' => 255],
			[['no_pesandokrm'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pesandokrm_id' => Yii::t('fe', 'Pesandokrm ID'),
			'no_pesandokrm' => Yii::t('fe', 'No Pemesanan'),
			'ruanganpemesan_id' => Yii::t('fe', 'Instalasi Tujuan Pemesanan'),
			'ruangantujuan_id' => Yii::t('fe', 'Ruangan Tujuan Pemesanan'),
			'tgl_pesandokrm' => Yii::t('fe', 'Tanggal Pemesanan'),
			'tgl_mintakirim' => Yii::t('fe', 'Tanggal Minta Dikirim'),
			'status_pesan' => Yii::t('fe', 'Status Pesan'),
			'additional_data' => Yii::t('fe', 'Additional Data'),
			'created_date' => Yii::t('fe', 'Created Date'),
			'created_by' => Yii::t('fe', 'Created By'),
			'modified_count' => Yii::t('fe', 'Modified Count'),
			'last_modified_date' => Yii::t('fe', 'Last Modified Date'),
			'last_modified_by' => Yii::t('fe', 'Last Modified By'),
			'is_deleted' => Yii::t('fe', 'Is Deleted'),
			'is_active' => Yii::t('fe', 'Is Active'),
			'deleted_date' => Yii::t('fe', 'Deleted Date'),
			'deleted_by' => Yii::t('fe', 'Deleted By'),
		];
	}
}
?>