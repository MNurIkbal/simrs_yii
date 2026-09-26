<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-06 14:20:59
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-06 14:29:08
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "diagnosa_v".
 *
 * @property int $diagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $klasifikasidiagnosa_nama
 * @property string $dtd_nama
 */
class DiagnosaView extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'diagnosa_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['diagnosa_id', 'jeniskasuspenyakit_id'], 'default', 'value' => null],
			[['diagnosa_id', 'jeniskasuspenyakit_id'], 'integer'],
			[['diagnosa_kode'], 'string', 'max' => 10],
			[['diagnosa_nama', 'diagnosa_namalainnya'], 'string', 'max' => 200],
			[['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
			[['klasifikasidiagnosa_nama'], 'string', 'max' => 500],
			[['dtd_nama'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'diagnosa_id' => 'Diagnosa ID',
			'diagnosa_kode' => 'Diagnosa Kode',
			'diagnosa_nama' => 'Diagnosa Nama',
			'diagnosa_namalainnya' => 'Diagnosa Namalainnya',
			'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
			'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
			'klasifikasidiagnosa_nama' => 'Klasifikasidiagnosa Nama',
			'dtd_nama' => 'Dtd Nama',
		];
	}
}
