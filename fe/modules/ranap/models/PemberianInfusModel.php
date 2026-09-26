<?php

namespace app\modules\ranap\models;

use Yii;

class PemberianInfusModel extends \yii\base\Model
{
	/**
	 * {@inheritdoc}
	 */
	public $tgl_pemasangan;
	public $instruksitindakan_id;
	public $instruksitindakanbmhp_id;
	public $volume;
	public $durasi;
	public $jumlah_tetesan;
	public $titik_pemasangan;


	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['tgl_pemasangan','instruksitindakan_id','instruksitindakanbmhp_id','volume','durasi','titik_pemasangan'], 'required'],
			[['instruksitindakan_id','volume','durasi','jumlah_tetesan'], 'number'],
			[['jumlah_tetesan'], 'safe'],
			[['tgl_pemasangan','titik_pemasangan'], 'string'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'tgl_pemasangan' => 'Tanggal Pemasangan',
			'instruksitindakan_id' => 'Nama Tindakan',
			'instruksitindakanbmhp_id' => 'Nama Obat',
			'volume' => 'Volume Infus',
			'durasi' => 'Durasi',
			'jumlah_tetesan' => 'Jumlah Tetesan',
			'titik_pemasangan' => 'Titik Pemasangan',
		];
	}
}
?>