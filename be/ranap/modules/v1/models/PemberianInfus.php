<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-13 13:15:46
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-13 13:16:02
 */

namespace app\modules\v1\models;

use Yii;

class PemberianInfus extends \Doco\components\DocoActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'pemberianinfus_t';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['pendaftaran_id', 'pasienadmisi_id', 'pegawai_id', 'tgl_pemasangan','instruksitindakan_id','instruksitindakanbmhp_id','volume','durasi','titik_pemasangan'], 'required'],
			[[ 'created_by', 'modified_count', 'last_modified_by', 'deleted_by',], 'default', 'value' => null],
			[['created_date', 'last_modified_date', 'deleted_date', 'jumlah_tetesan'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['tgl_pemasangan', 'titik_pemasangan', 'additional_data'], 'string'],
			[['volume','durasi','jumlah_tetesan'], 'number'],
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