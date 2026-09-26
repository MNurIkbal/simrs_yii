<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanFisikForm extends \yii\base\Model
{
	public $pendaftaran_id;
	public $berat_badan;
	public $tinggi_badan;
	public $td_sistolik;
	public $td_diastolik;
	public $pernafasan;
	public $detak_nadi;
	public $suhu;
	public $mata_kanan;
	public $mata_kanan_catatan;
	public $mata_kiri;
	public $mata_kiri_catatan;
	public $telinga_kanan;
	public $telinga_kanan_catatan;
	public $telinga_kiri;
	public $telinga_kiri_catatan;
	public $jantung;
	public $jantung_catatan;

	public $ekg;
	public $ekg_catatan;
	public $paru;
	public $paru_catatan;
	public $hepar;
	public $hepar_catatan;
	public $lien;
	public $lien_catatan;
	public $ginjal;
	public $ginjal_catatan;
	public $motorik;
	public $motorik_catatan;
	public $sensorik;
	public $sensorik_catatan;
	public $pemeriksaan_lainnya;
	public $imt;
	public $kategori_bb;
	public $td_kategori;
	
	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[
					'pendaftaran_id', 'berat_badan', 'tinggi_badan', 'td_sistolik', 
					'td_diastolik', 'pernafasan', 'detak_nadi', 'suhu'
				], 
				'required'
			],

			[
				[	'pendaftaran_id', 'berat_badan', 'tinggi_badan', 
					'td_sistolik', 'td_diastolik','pernafasan',
					'detak_nadi','suhu','mata_kanan',
					'mata_kanan_catatan','mata_kiri','mata_kiri_catatan',
					'telinga_kanan','telinga_kanan_catatan','telinga_kiri',
					'telinga_kiri_catatan','jantung','jantung_catatan',
					'ekg','ekg_catatan','paru',
					'paru_catatan','hepar','hepar_catatan',
					'lien','lien_catatan','ginjal',
					'ginjal_catatan','motorik','motorik_catatan',
					'sensorik','sensorik_catatan','pemeriksaan_lainnya', 'imt', 'kategori_bb', 'td_kategori'
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'td_sistolik' => Yii::t('fe', 'Tekanan Darah Sistolik'),
            'td_diastolik' => Yii::t('fe', 'Tekanan Darah Diastolik'),
            'imt' => Yii::t('fe', 'Imt'),
            'kategori_bb' => Yii::t('fe', 'Kategori Berat Badan'),
            'td_kategori' => Yii::t('fe', 'Tekanan Darah Kategori'),
		];
	}
}
