<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanGigiForm extends \yii\base\Model
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

	public $caries;
	public $fillings;
	public $root;
	public $missing_tooth;
	public $crown;
	public $bridge;
	public $dentures;
	public $malloclusion;
	public $lack_contact;
	public $calculus;
	public $edentolous;
	public $mobility;
	public $impacted_tooth;
	public $prosthesis;
	public $others;

	public $catatan_caries;
	public $catatan_fillings;
	public $catatan_root;
	public $catatan_missing_tooth;
	public $catatan_crown;
	public $catatan_bridge;
	public $catatan_dentures;
	public $catatan_malloclusion;
	public $catatan_lack_contact;
	public $catatan_calculus;
	public $catatan_edentolous;
	public $catatan_mobility;
	public $catatan_impacted_tooth;
	public $catatan_prosthesis;
	public $catatan_others;
	public $attribute_gigi = [
		'caries',
		'fillings',
		'root',
		'missing_tooth',
		'crown',
		'bridge',
		'dentures',
		'malloclusion',
		'lack_contact',
		'calculus',
		'edentolous',
		'mobility',
		'impacted_tooth',
		'prosthesis',
		'others',
		'catatan_caries', 'catatan_fillings', 
    'catatan_root', 'catatan_missing_tooth', 'catatan_crown', 'catatan_bridge', 
    'catatan_dentures', 'catatan_malloclusion', 'catatan_lack_contact', 
    'catatan_calculus', 'catatan_edentolous', 'catatan_mobility',
    'catatan_impacted_tooth', 'catatan_prosthesis', 'catatan_others'
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
					'caries', 'fillings', 'root', 'missing_tooth', 'crown', 'bridge', 'dentures',
					'malloclusion', 'lack_contact', 'calculus', 'edentolous', 'mobility',
					'impacted_tooth', 'prosthesis', 'others', 'catatan_caries', 'catatan_fillings', 
					'catatan_root', 'catatan_missing_tooth', 'catatan_crown', 'catatan_bridge', 
					'catatan_dentures', 'catatan_malloclusion', 'catatan_lack_contact', 
					'catatan_calculus', 'catatan_edentolous', 'catatan_mobility',
					'catatan_impacted_tooth', 'catatan_prosthesis', 'catatan_others',
					'attribute_gigi', 'kesimpulan', 'anjuran',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'pernafasan' => Yii::t('fe', 'Pernapasan'),
            'root' => Yii::t('fe', 'Root / Radix'),
            'crown' => Yii::t('fe', 'Crown / Casting'),
            'bridge' => Yii::t('fe', 'Bridge Work'),
            'lack_contact' => Yii::t('fe', 'Lack of Contact'),
		];
	}
}
