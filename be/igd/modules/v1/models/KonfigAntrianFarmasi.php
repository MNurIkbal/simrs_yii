<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-20 16:29:52
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigantrianfarmasi_v".
 *
 * @property int $konfigantrian_id
 * @property int $layarantrian_id
 * @property int $jenisantrian_id
 * @property int $fungsiantrian_id
 * @property int $carabayar_id
 * @property string $layarantrian_nama
 * @property string $jenis_antrian
 * @property string $fungsi_antrian
 * @property string $lookup_value
 * @property string $carabayar_nama
 * @property bool $is_default
 * @property bool $is_penjamin
 * @property string $kode_antrian
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $groupcarabayar_id
 * @property string $group_carabayar
 */
class KonfigAntrianFarmasi extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'konfigantrianfarmasi_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['konfigantrian_id', 'layarantrian_id', 'jenisantrian_id', 'fungsiantrian_id', 'carabayar_id', 'instalasi_id', 'groupcarabayar_id'], 'default', 'value' => null],
			[['konfigantrian_id', 'layarantrian_id', 'jenisantrian_id', 'fungsiantrian_id', 'carabayar_id', 'instalasi_id', 'groupcarabayar_id'], 'integer'],
			[['is_default', 'is_penjamin'], 'boolean'],
			[['layarantrian_nama'], 'string', 'max' => 100],
			[['jenis_antrian', 'fungsi_antrian', 'lookup_value', 'group_carabayar'], 'string', 'max' => 200],
			[['carabayar_nama', 'instalasi_nama'], 'string', 'max' => 50],
			[['kode_antrian'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'konfigantrian_id' => 'Konfigantrian ID',
			'layarantrian_id' => 'Layarantrian ID',
			'jenisantrian_id' => 'Jenisantrian ID',
			'fungsiantrian_id' => 'Fungsiantrian ID',
			'carabayar_id' => 'Carabayar ID',
			'layarantrian_nama' => 'Layarantrian Nama',
			'jenis_antrian' => 'Jenis Antrian',
			'fungsi_antrian' => 'Fungsi Antrian',
			'lookup_value' => 'Lookup Value',
			'carabayar_nama' => 'Carabayar Nama',
			'is_default' => 'Is Default',
			'is_penjamin' => 'Is Penjamin',
			'kode_antrian' => 'Kode Antrian',
			'instalasi_id' => 'Instalasi ID',
			'instalasi_nama' => 'Instalasi Nama',
			'groupcarabayar_id' => 'Groupcarabayar ID',
			'group_carabayar' => 'Group Carabayar',
		];
	}
}