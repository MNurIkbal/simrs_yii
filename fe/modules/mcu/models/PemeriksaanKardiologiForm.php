<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanKardiologiForm extends \yii\base\Model
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
	public $tumor;
	public $jvp;
	public $paru;
	public $jantung;
	public $hati;
	public $limpa;
	public $lainnya;
	public $edema;
	public $rontgen_thorax;
	public $ekg;
	public $echocardiografi;
	public $treadmill;
	public $attribute_kardiologi = [
		'tumor', 'jvp', 'paru', 'jantung', 'hati', 'limpa', 'lainnya', 
    	'edema', 'rontgen_thorax', 'ekg', 'echocardiografi', 'treadmill'
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
					'tumor', 'jvp', 'paru', 'jantung', 'hati', 'limpa', 'lainnya', 
					'edema', 'rontgen_thorax', 'ekg', 'echocardiografi', 'kesimpulan', 'anjuran',
					'attribute_kardiologi', 'treadmill'
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'pernafasan' => Yii::t('fe', 'Pernapasan'),
		];
	}
}
