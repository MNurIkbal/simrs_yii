<?php

/**
 * @Author: Iqbal
 * @Date:   2018-07-18 13:23:44
 * @Last Modified by:   Iqbal
 * @Last Modified time: 2018-07-18 13:25:48
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotarifrs_v".
 *
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $jenistarif_id
 * @property int $perdatarif_id
 * @property int $kelompoktindakan_id
 * @property int $kategoritindakan_id
 * @property int $daftartindakan_id
 * @property int $kelaspelayanan_id
 * @property int $komponentarif_id
 * @property string $ruangan_nama
 * @property string $jenistarif_nama
 * @property string $kelompoktindakan_nama
 * @property string $kategoritindakan_nama
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property string $kelaspelayanan_nama
 * @property string $perdanama_sk
 * @property string $komponentarif_nama
 * @property int $persencyto_tindakan
 * @property int $persendiskon_tindakan
 * @property double $harga_tariftindakan
 * @property int $penjamin_id
 * @property string $tipepaket_nama
 * @property int $tipepaket_id
 */
class InfoTarifRs extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'infotarifrs_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['ruangan_id', 'instalasi_id', 'jenistarif_id', 'perdatarif_id', 'kelompoktindakan_id', 'kategoritindakan_id', 'daftartindakan_id', 'kelaspelayanan_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan', 'penjamin_id', 'tipepaket_id'], 'default', 'value' => null],
			[['ruangan_id', 'instalasi_id', 'jenistarif_id', 'perdatarif_id', 'kelompoktindakan_id', 'kategoritindakan_id', 'daftartindakan_id', 'kelaspelayanan_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan', 'penjamin_id', 'tipepaket_id'], 'integer'],
			[['harga_tariftindakan'], 'number'],
			[['ruangan_nama', 'kelompoktindakan_nama', 'kelaspelayanan_nama', 'tipepaket_nama'], 'string', 'max' => 50],
			[['jenistarif_nama', 'komponentarif_nama'], 'string', 'max' => 25],
			[['kategoritindakan_nama'], 'string', 'max' => 150],
			[['daftartindakan_kode'], 'string', 'max' => 20],
			[['daftartindakan_nama', 'perdanama_sk'], 'string', 'max' => 200],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'ruangan_id' => 'Ruangan ID',
			'instalasi_id' => 'Instalasi ID',
			'jenistarif_id' => 'Jenistarif ID',
			'perdatarif_id' => 'Perdatarif ID',
			'kelompoktindakan_id' => 'Kelompoktindakan ID',
			'kategoritindakan_id' => 'Kategoritindakan ID',
			'daftartindakan_id' => 'Daftartindakan ID',
			'kelaspelayanan_id' => 'Kelaspelayanan ID',
			'komponentarif_id' => 'Komponentarif ID',
			'ruangan_nama' => 'Ruangan Nama',
			'jenistarif_nama' => 'Jenistarif Nama',
			'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
			'kategoritindakan_nama' => 'Kategoritindakan Nama',
			'daftartindakan_kode' => 'Daftartindakan Kode',
			'daftartindakan_nama' => 'Daftartindakan Nama',
			'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
			'perdanama_sk' => 'Perdanama Sk',
			'komponentarif_nama' => 'Komponentarif Nama',
			'persencyto_tindakan' => 'Persencyto Tindakan',
			'persendiskon_tindakan' => 'Persendiskon Tindakan',
			'harga_tariftindakan' => 'Harga Tariftindakan',
			'penjamin_id' => 'Penjamin ID',
			'tipepaket_nama' => 'Tipepaket Nama',
			'tipepaket_id' => 'Tipepaket ID',
		];
	}
}
?>