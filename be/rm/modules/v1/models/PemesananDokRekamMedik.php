<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-16 11:09:54
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-18 10:31:00
 */

namespace app\modules\v1\models;

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
class PemesananDokRekamMedik extends \Doco\components\DocoActiveRecord
{
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
			'pesandokrm_id' => Yii::t('app', 'Pesandokrm ID'),
			'no_pesandokrm' => Yii::t('app', 'No Pesandokrm'),
			'ruanganpemesan_id' => Yii::t('app', 'Ruanganpemesan ID'),
			'ruangantujuan_id' => Yii::t('app', 'Ruangantujuan ID'),
			'tgl_pesandokrm' => Yii::t('app', 'Tgl Pesandokrm'),
			'tgl_mintakirim' => Yii::t('app', 'Tgl Mintakirim'),
			'status_pesan' => Yii::t('app', 'Status Pesan'),
			'additional_data' => Yii::t('app', 'Additional Data'),
			'created_date' => Yii::t('app', 'Created Date'),
			'created_by' => Yii::t('app', 'Created By'),
			'modified_count' => Yii::t('app', 'Modified Count'),
			'last_modified_date' => Yii::t('app', 'Last Modified Date'),
			'last_modified_by' => Yii::t('app', 'Last Modified By'),
			'is_deleted' => Yii::t('app', 'Is Deleted'),
			'is_active' => Yii::t('app', 'Is Active'),
			'deleted_date' => Yii::t('app', 'Deleted Date'),
			'deleted_by' => Yii::t('app', 'Deleted By'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDetail()
	{
		return $this->hasMany(PemesananDokRekamMedikDetail::className(), ['pesandokrm_id' => 'pesandokrm_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getInstalasi()
	{
		return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'ruanganpemesan_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRuangan()
	{
		return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangantujuan_id']);
	}
}
?>