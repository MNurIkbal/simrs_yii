<?php

namespace Doco\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class RiwayatPenyakitKramatForm extends DocoBaseModel
{
	public $riwayat;
	public $riwayat_lainnya;
	public $pendaftaran_id;

	

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[	'riwayat', 'riwayat_lainnya', 'pendaftaran_id'
				],
				'safe'
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			
		];
	}
}
?>