<?php
namespace app\modules\kasir\models;

use Yii;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-17 16:34:48
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-02 10:03:10
 */


class FilterPemesananObatForm extends \yii\base\Model
{

	public $tgl_mulai;
	public $tgl_selesai;
	public $instalasi_tujuan;
	public $ruangan_tujuan;
	public $nomor_pemesanan;

	public function rules(){
		return [
			[['tgl_mulai','tgl_selesai','instalasi_tujuan','ruangan_tujuan'], 'required'],
			[['nomor_pemesanan'],'safe']
		];
	}

	public function attributeLabels(){
		return [
			'tgl_mulai'=>Yii::t('fe','tgl mulai'),
			'tgl_selesai'=>Yii::t('fe','tgl selesai'),
			'instalasi'=>Yii::t('fe','Instalasi'),
			'ruangan'=>Yii::t('fe','Ruangan'),
			'nomor_pemesanan'=>Yii::t('fe','no pemesanan')
		];
	}

}