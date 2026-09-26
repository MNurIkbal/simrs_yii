<?php

/**
 * @Author: Wahyu Saepuloh
 * digunakan untuk validate SQL injection - temuan Pentest
 */

namespace app\modules\v1\models;

use Yii;
class ValidateInjention extends \yii\base\Model
{
	public $pendaftaran_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pendaftaran_id'],'number', 'message' => 'ID Pendaftaran tidak berupa Angka!'],
			[['pendaftaran_id'],'safe'],
		];
	}

	public function attributeLabels()
	{
		return [
			'pendaftaran_id' => 'Pendaftaran ID'
		];
	}
}