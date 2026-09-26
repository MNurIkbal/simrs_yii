<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanObsgynForm extends \yii\base\Model
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

	public $menarche;
	public $lama_haid;
	public $gangguan_haid;
	public $dysmenorrhoe;
	public $dyspaeruni;
	public $kontrasepsi;
	public $flour_albus;
	public $fiuxus;
	public $gangguan_miksi;
	public $gangguan_defikasi;
	public $lainlain;
	public $anak_hidup;
	public $prematur;
	public $abdomen;
	public $vulva;
	public $vagina;
	public $cervix_uteri;
	public $corpus_uteri;
	public $adnex;
	public $culdesac;
	public $papsmear;
	public $kolposkopi;
	public $attribute_obsgyn = [
		'keluhan',
		'menarche',
		'lama_haid',
		'gangguan_haid',
		'dysmenorrhoe',
		'dyspaeruni',
		'kontrasepsi',
		'flour_albus',
		'fiuxus',
		'gangguan_miksi',
		'gangguan_defikasi',
		'lainlain',
		'abdomen',
		'vulva',
		'vagina',
		'cervix_uteri',
		'corpus_uteri',
		'adnex',
		'culdesac',
		'papsmear',
		'kolposkopi',
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
					'detak_nadi', 'pernafasan', 'suhu', 'keluhan', 'menarche',
					'lama_haid', 'gangguan_haid', 'dysmenorrhoe','dyspaeruni',
					'kontrasepsi','flour_albus','fiuxus','gangguan_miksi',
					'gangguan_defikasi','lainlain','anak_hidup','prematur',
					'abdomen','vulva','vagina','cervix_uteri',
					'corpus_uteri','adnex','culdesac','papsmear',
					'kolposkopi','attribute_obsgyn', 'kesimpulan', 'anjuran',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
    	'pernafasan' => Yii::t('fe', 'Pernapasan'),
    	'lainlain' => Yii::t('fe', 'Lain-Lain'),
    	'papsmear' => Yii::t('fe', "Pap's Smear"),
    	'culdesac' => Yii::t('fe', 'Cul De Sac'),
    	'fiuxus' => Yii::t('fe', 'Fluxus'),
		];
	}
}
