<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-22 11:36:26
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-15 16:32:14
 */

namespace Doco\master\models;

use Yii;

class PegawaiRuanganForm extends \yii\base\Model
{
	public $is_active;
	public $instalasi_id;
	public $nama_pegawai;
	public $pegawai_id;
	public $ruangan_id;
	public $ruangan_nama;

	public function rules()
	{
		return [
			[['instalasi_id','ruangan_id','pegawai_id'], 'required','message'=>'{attribute} Tidak boleh kosong'],
			[['instalasi_id','ruangan_id','pegawai_id'], 'integer'],
			[['is_active', 'nama_pegawai', 'ruangan_nama'],'safe']
		];
	}

	public function attributeLabels()
	{
		return [
			'instalasi_id'=>\Yii::t('fe','Instalasi'),
			'ruangan_id'=>\Yii::t('fe','Ruangan'),
			'pegawai_id'=>\Yii::t('fe','Nama pegawai'),
			'is_active'=>\Yii::t('fe','Aktif'),
		];
	}

}
