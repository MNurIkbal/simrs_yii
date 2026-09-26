<?php

namespace app\modules\igd\models;

use Yii;
class ImplementasiTindakanForm extends \yii\base\Model
{
	public $perawat1_id;
	public $perawat2_id;
	public $jml_diimplementasi;
	public $tarif_satuan;
	public $tarif_cyto;
	public $tgl_implementasi;
	public $instruksitindakan_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['jml_diimplementasi'],'number','min'=>1],
			[['perawat1_id','perawat2_id','jml_diimplementasi','tarif_satuan','tarif_cyto','tgl_implementasi','instruksitindakan_id'],'safe'],
		];
	}

	public function attributeLabels()
	{
		return [
			'jml_diimplementasi' => 'Jumlah Implementasi'
		];
	}
}