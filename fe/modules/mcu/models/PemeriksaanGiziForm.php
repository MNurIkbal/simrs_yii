<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanGiziForm extends \yii\base\Model
{
	public $pendaftaran_id;
	public $pasien_id;
	public $ruangan_id;
	public $berat_badan;
	public $tinggi_badan;
	public $td_sistolik;
	public $td_diastolik;
	public $detak_nadi;
	public $pernafasan;
	public $suhu;

	public $keluhan;
	public $kesimpulan;
	public $anjuran;

	public $pemeriksaanspesialismcu_id;

	public $penilaian_gizi;
	public $attribute_gizi = [
		'penilaian_gizi',
	];

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[
					'pendaftaran_id', 'ruangan_id', 'berat_badan', 'tinggi_badan', 
					'td_sistolik', 'td_diastolik', 'detak_nadi', 'pernafasan',  'suhu', 
				], 
				'required'
			],
			[
				[	'pendaftaran_id', 'berat_badan', 'tinggi_badan', 'td_sistolik', 
					'detak_nadi', 'pernafasan', 'suhu', 'keluhan',  
					'penilaian_gizi',
					'attribute_gizi', 'kesimpulan', 'anjuran',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'pernafasan' => Yii::t('fe', 'Pernapasan'),
            'penilaian_gizi' => Yii::t('fe', 'Penilaian Gizi'),
            'kesimpulan' => Yii::t('fe', 'KESIMPULAN'),
            'anjuran' => Yii::t('fe', 'ANJURAN'),
		];
	}
}
