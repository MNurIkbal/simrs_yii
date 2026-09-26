<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-07 10:06:41
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-07 15:47:01
 */
namespace Doco\apotek\models;

use Yii;

class StokForm extends \yii\base\Model
{
	public $stok_fisik;
	public $kondisi;
	public $tgl_kadaluarsa;
	public $total_harganetto_sistem;
	public $total_harganetto_fisik;
	public $jenis_stokopname;
	public $selisih_harganetto;
	public $harga_satuan;
	public $obatalkes_id;
	public $stok_sistem;
	public $pegawai_id;
	public $formuliropname_id;
	public $nobatch;



	public function attributeLabels(){
		return [
			'stok_fisik'=> Yii::t('fe','Stok Fisik'),
			'kondisi'=> Yii::t('fe','Kondisi'),
			'tgl_kadaluarsa'=> Yii::t('fe','Tanggal Kadaluarsa'),
			'total_harganetto_sistem'=> Yii::t('fe','Total Harga Netto Sistem'),
			'total_harganetto_fisik'=> Yii::t('fe','Total Harga Netto Fisik'),
			'jenis_stokopname'=> Yii::t('fe','Jenis Stok Opname'),
			'selisih_harganetto'=> Yii::t('fe','Selisih Harga Netto')
		];
	}
}

