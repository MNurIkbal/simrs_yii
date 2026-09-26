<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-13 13:15:46
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-13 13:16:02
 */

namespace app\modules\v1\models;

use Yii;

class PemberianInfusRespon extends \Doco\components\DocoActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'pemberianinfusrespon_t';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['pemberianinfus_id', 'tgl_respon', 'pegawai_id', 'respon'], 'required'],
			[['created_by', 'modified_count', 'last_modified_by', 'deleted_by',], 'default', 'value' => null],
			[['created_date', 'last_modified_date', 'deleted_date', 'jumlah_tetesan'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['tgl_respon', 'respon', 'additional_data'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
		];
	}
}
?>