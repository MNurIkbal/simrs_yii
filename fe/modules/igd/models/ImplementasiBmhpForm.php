<?php

namespace app\modules\igd\models;

use Yii;
class ImplementasiBmhpForm extends \yii\base\Model
{
	public $perawat1_id;
	public $perawat2_id;
	public $jml_diimplementasi;
	public $tgl_implementasi;
	public $instruksitindakanbmhp_id;
	public $obatalkes_id;
	public $is_ditagihkan;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['jml_diimplementasi'],'number','min'=>1],
			[['perawat1_id','perawat2_id','jml_diimplementasi','tgl_implementasi','instruksitindakanbmhp_id','obatalkes_id','is_ditagihkan'],'safe'],
		];
	}

	public function attributeLabels()
	{
		return [
			'jml_diimplementasi' => 'Jumlah Implementasi'
		];
	}
}