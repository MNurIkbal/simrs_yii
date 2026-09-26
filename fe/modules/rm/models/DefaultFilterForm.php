<?php

namespace app\modules\rm\models;

use Yii;


/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 11:56:24
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 11:57:25
 */

class DefaultFilterForm extends \yii\base\Model
{

	public $tgl_mulai;
	public $tgl_selesai;
	public $instalasi_tujuan;
	public $ruangan_tujuan;
	public $nomor;

	public function rules(){
		return [
			[['tgl_mulai','tgl_selesai','instalasi_tujuan','ruangan_tujuan'], 'required'],
			[['nomor'],'safe']
		];
	}

	public function attributeLabels(){
		return [
			'tgl_mulai'=>Yii::t('fe','tgl mulai'),
			'tgl_selesai'=>Yii::t('fe','tgl selesai'),
			'instalasi'=>Yii::t('fe','Instalasi'),
			'ruangan'=>Yii::t('fe','Ruangan'),
			'nomor'=>'Nomor'
		];
	}

}