<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 13:41:54
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-12 13:42:05
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "signaobat_m".
 *
 * @property int $signa_id
 * @property string $signa_nama
 * @property string $signa_namalainnya
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
class SignaObat extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'signaobat_m';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['signa_id', 'signa_nama'], 'required'],
			[['signa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['signa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['additional_data'], 'string'],
			[['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['signa_nama', 'signa_namalainnya'], 'string', 'max' => 255],
			[['signa_id'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'signa_id' => 'Signa ID',
			'signa_nama' => 'Signa Nama',
			'signa_namalainnya' => 'Signa Namalainnya',
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