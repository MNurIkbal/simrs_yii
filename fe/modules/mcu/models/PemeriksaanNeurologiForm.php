<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanNeurologiForm extends \yii\base\Model
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

	public $kesadaran;
	public $tanda_rangsang;
	public $tanda_peninggian;
	public $saraf_otak;
	public $sistem_motorik;
	public $refleks_fisiologis;
	public $refleks_patologis;
	public $sistem_sensorik;
	public $sistem_otonom;
	public $sistem_luhur;
	public $lainlain;
	public $eeg;
	public $attribute_neurologi = [
		'kesadaran',
		'tanda_rangsang',
		'tanda_peninggian',
		'saraf_otak',
		'sistem_motorik',
		'refleks_fisiologis',
		'refleks_patologis',
		'sistem_sensorik',
		'sistem_otonom',
		'sistem_luhur',
		'lainlain',
		'eeg'
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
					'kesadaran', 'tanda_rangsang', 'tanda_peninggian', 'saraf_otak', 'sistem_motorik', 'refleks_fisiologis', 'refleks_patologis',
					'sistem_sensorik', 'sistem_otonom', 'sistem_luhur', 'lainlain', 'eeg',
					'attribute_neurologi', 'kesimpulan', 'anjuran',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'pernafasan' => Yii::t('fe', 'Pernapasan'),
            'kesadaran' => Yii::t('fe', 'Kesadaran'),
            'tanda_rangsang' => Yii::t('fe', 'Tanda-tanda rangsang selaput otak'),
            'tanda_peninggian' => Yii::t('fe', 'Tanda-tanda peninggian tekanan intrakranial'),
            'saraf_otak' => Yii::t('fe', 'Saraf-saraf Otak'),
            'sistem_motorik' => Yii::t('fe', 'Sistem Motorik'),
            'refleks_fisiologis' => Yii::t('fe', 'Refleks Fisiologis'),
            'refleks_patologis' => Yii::t('fe', 'Refleks Patologis'),
            'sistem_sensorik' => Yii::t('fe', 'Sistem Sensorik'),
            'sistem_otonom' => Yii::t('fe', 'Sistem Otonom'),
            'sistem_luhur' => Yii::t('fe', 'Sistem Luhur'),
            'lainlain' => Yii::t('fe', 'Lain-lain'),
            'eeg' => Yii::t('fe', 'EEG'),
            'kesimpulan' => Yii::t('fe', 'KESIMPULAN'),
            'anjuran' => Yii::t('fe', 'ANJURAN'),
		];
	}
}
