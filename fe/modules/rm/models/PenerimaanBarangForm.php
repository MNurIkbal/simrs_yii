<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 14:22:35
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 14:25:33
 */

namespace app\modules\rm\models;

use Yii;

class PenerimaanBarangForm extends \yii\base\Model
{

	public $nomor_mutasi;
	public $pegawai_mengetahui;
	public $pegawai_menyetujui;

	public function rules()
	{
		return [
			[['nomor_mutasi','pegawai_menyetujui','pegawai_mengetahui'], 'required'],
		];
	}
	public function attributeLabels()
	{
		return [
			'nomor_mutasi'=>'Nomor Mutasi',
			'pegawai_mengetahui'=>'Pegawai Mengetahui',
			'pegawai_menyetujui'=>'Pegawai Menyetujui'
		];
	}

}