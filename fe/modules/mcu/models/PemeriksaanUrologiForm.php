<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanUrologiForm extends \yii\base\Model
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

	public $abdomen;
	public $regio_costovertebralis;
	public $regio_lumbal;
	public $regio_suprapubik;
	public $penis;
	public $skrotum;
	public $testis;
	public $prostat;
	public $transiluminasi;
	public $uroflowmetri;
	public $lainlain;
	public $attribute_urologi = [
        'abdomen',
        'regio_costovertebralis',
        'regio_lumbal',
        'regio_suprapubik',
        'penis',
        'skrotum',
        'testis',
        'prostat',
        'transiluminasi',
        'uroflowmetri',
        'lainlain'
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
					'abdomen', 'regio_costovertebralis', 'regio_lumbal', 'regio_suprapubik', 'penis', 'skrotum', 'testis',
					'prostat', 'transiluminasi', 'uroflowmetri', 'lainlain',
					'attribute_urologi', 'kesimpulan', 'anjuran',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'pernafasan' => Yii::t('fe', 'Pernapasan'),
            'abdomen' => Yii::t('fe', 'Abdomen'),
            'regio_costovertebralis' => Yii::t('fe', 'Regio Costovertebralis'),
            'regio_lumbal' => Yii::t('fe', 'Regio Lumbal'),
            'regio_suprapubik' => Yii::t('fe', 'Regio Suprapubik'),
            'penis' => Yii::t('fe', 'Penis'),
            'skrotum' => Yii::t('fe', 'Skrotum'),
            'testis' => Yii::t('fe', 'Testis'),
            'prostat' => Yii::t('fe', 'Prostat'),
            'transiluminasi' => Yii::t('fe', 'Transiluminasi'),
            'uroflowmetri' => Yii::t('fe', 'Uroflowmetri'),
            'lainlain' => Yii::t('fe', 'Lain-lain'),
            'kesimpulan' => Yii::t('fe', 'KESIMPULAN'),
            'anjuran' => Yii::t('fe', 'ANJURAN'),
		];
	}
}
