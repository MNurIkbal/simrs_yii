<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:19:16
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-24 16:31:08
 */

// Namespace
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemberianobatdetail_v".
 *
 * @property int $pemberianobat_id
 * @property int $pemberianobatdetail_id
 * @property int $jenisobat_id
 * @property string $no_res_rekon
 * @property int $obatalkes_id
 * @property string $nama_obat
 * @property string $signa_obat
 * @property int $jumlah
 * @property string $dokter
 * @property string $wkt_pemberian
 * @property string $pemberi1
 * @property string $pemberi2
 * @property string $efek
 * @property string $keterangan
 */
class PemberianObatDetailView extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'pemberianobatdetail_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pemberianobat_id', 'pemberianobatdetail_id', 'jenisobat_id', 'obatalkes_id', 'jumlah'], 'default', 'value' => null],
			[['pemberianobat_id', 'pemberianobatdetail_id', 'jenisobat_id', 'obatalkes_id', 'jumlah'], 'integer'],
			[['wkt_pemberian'], 'safe'],
			[['no_res_rekon', 'nama_obat', 'efek', 'keterangan'], 'string', 'max' => 255],
			[['signa_obat'], 'string', 'max' => 100],
			[['dokter', 'pemberi1', 'pemberi2'], 'string', 'max' => 50],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pemberianobat_id' => 'Pemberianobat ID',
			'pemberianobatdetail_id' => 'Pemberianobatdetail ID',
			'jenisobat_id' => 'Jenisobat ID',
			'no_res_rekon' => 'No Res Rekon',
			'obatalkes_id' => 'Obatalkes ID',
			'nama_obat' => 'Nama Obat',
			'signa_obat' => 'Signa Obat',
			'jumlah' => 'Jumlah',
			'dokter' => 'Dokter',
			'wkt_pemberian' => 'Wkt Pemberian',
			'pemberi1' => 'Pemberi1',
			'pemberi2' => 'Pemberi2',
			'efek' => 'Efek',
			'keterangan' => 'Keterangan',
		];
	}
}
?>