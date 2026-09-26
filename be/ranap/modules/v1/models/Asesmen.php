<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "asesmen_m".
 *
 * @property int $asesmen_id
 * @property int $parent_id
 * @property string $level
 * @property string $asesmen_nama
 * @property string $jenis_input
 * @property string $opsi
 * @property string $kondisi
 * @property int $urutan
 * @property string $tabulasi
 * @property string $sumber_tabel
 * @property bool $is_mandatory
 * @property int $instalasi_id
 * @property string $formula
 * @property string $menu_header
 * @property string $wizard
 * @property string $total_score
 */
class Asesmen extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'asesmen_m';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['asesmen_id'], 'required'],
			[['asesmen_id', 'parent_id', 'urutan', 'instalasi_id'], 'default', 'value' => null],
			[['asesmen_id', 'parent_id', 'urutan', 'instalasi_id'], 'integer'],
			[['opsi', 'kondisi', 'formula'], 'string'],
			[['is_mandatory'], 'boolean'],
			[['level'], 'string', 'max' => 100],
			[['asesmen_nama', 'jenis_input', 'tabulasi', 'sumber_tabel', 'menu_header', 'wizard', 'total_score'], 'string', 'max' => 255],
			[['asesmen_id'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'asesmen_id' => 'Asesmen ID',
			'parent_id' => 'Parent ID',
			'level' => 'Level',
			'asesmen_nama' => 'Asesmen Nama',
			'jenis_input' => 'Jenis Input',
			'opsi' => 'Opsi',
			'kondisi' => 'Kondisi',
			'urutan' => 'Urutan',
			'tabulasi' => 'Tabulasi',
			'sumber_tabel' => 'Sumber Tabel',
			'is_mandatory' => 'Is Mandatory',
			'instalasi_id' => 'Instalasi ID',
			'formula' => 'Formula',
			'menu_header' => 'Menu Header',
			'wizard' => 'Wizard',
			'total_score' => 'Total Score',
		];
	}
}
?>