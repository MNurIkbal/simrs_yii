<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanThtForm extends \yii\base\Model
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

	public $daun_telinga_kanan;
	public $daun_telinga_kiri;
	public $liang_telinga_kanan;
	public $liang_telinga_kiri;
	public $membran_tympani_kanan;
	public $membran_tympani_kiri;
	public $audiogram_kanan;
	public $audiogram_kiri;
	public $hidung;
	public $tenggorokan;
	public $nasoendoskopi;
	public $leher;
	public $attribute_tht = [
		'daun_telinga_kanan', 'daun_telinga_kiri', 'liang_telinga_kanan', 
		'liang_telinga_kiri', 'membran_tympani_kanan', 'membran_tympani_kiri', 
		'audiogram_kanan', 'audiogram_kiri',
		'hidung', 'tenggorokan', 'nasoendoskopi', 'leher',
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
					'daun_telinga_kanan', 'daun_telinga_kiri', 'liang_telinga_kanan', 
					'liang_telinga_kiri', 'membran_tympani_kanan', 'membran_tympani_kiri', 
					'audiogram_kanan', 'audiogram_kiri', 'hidung', 'tenggorokan', 
					'nasoendoskopi', 'leher', 'attribute_tht',
					'kesimpulan', 'anjuran',
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
