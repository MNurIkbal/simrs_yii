<?php


namespace app\modules\rm\models;

use Yii;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-17 10:32:18
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-17 10:48:50
 */

class FilterStokBarangForm extends \yii\base\Model
{

	public $tgl_mulai;
	public $tgl_selesai;
	public $instalasi;
	public $ruangan;
	public $nama_barang;

	public function rules(){
		return [
			[['tgl_mulai','tgl_selesai','instalasi','ruangan'], 'required'],
			[['nama_barang'],'safe']
		];
	}

	public function attributeLabels(){
		return [
			'tgl_mulai'=>Yii::t('fe','tgl mulai'),
			'tgl_selesai'=>Yii::t('fe','tgl selesai'),
			'instalasi'=>Yii::t('fe','Instalasi'),
			'ruangan'=>Yii::t('fe','Ruasssngan'),
			'nama_barang'=>Yii::t('fe','Nama Barang')
		];
	}

}
