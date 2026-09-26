<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-17 16:35:30
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-18 10:29:28
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pesandokrmdetail_t".
 *
 * @property int $pesandokrmdetail_id
 * @property int $pesandokrm_id
 * @property int $dokrekammedis_id
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
class PemesananDokRekamMedikDetail extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'pesandokrmdetail_t';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pesandokrm_id', 'dokrekammedis_id'], 'required'],
			[['pesandokrm_id', 'dokrekammedis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['pesandokrm_id', 'dokrekammedis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['additional_data'], 'string'],
			[['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pesandokrmdetail_id' => Yii::t('app', 'Pesandokrmdetail ID'),
			'pesandokrm_id' => Yii::t('app', 'Pesandokrm ID'),
			'dokrekammedis_id' => Yii::t('app', 'Dokrekammedis ID'),
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
	public function getInfoPosisiDokRekamMedik()
	{
		return $this->hasOne(InfoPosisiDokRekamMedik::className(), ['dokrekammedis_id' => 'dokrekammedis_id']);
	}
}
?>