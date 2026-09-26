<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanInternisForm extends \yii\base\Model
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

	public $riwayat_penyakit_dahulu;
  public $riwayat_penyakit_keluarga;
  public $riwayat_imunisasi;
  public $riwayat_kebiasaan;
  public $alergi;
  public $keadaan_umum;
  public $kulit;
  public $kelenjar_getah_bening;
  public $mata;
  public $hidung;
  public $telinga;
  public $mulut;
  public $tiroid;
  public $tumor;
  public $lainnya_leher;
  public $paru;
  public $jantung;
  public $hati;
  public $limpa;
  public $ginjal;
  public $lainnya_perut;
  public $lengan;
  public $tungkai;
  public $lainnya_extremitas;
	public $attribute_internis = [
    'riwayat_penyakit_dahulu','riwayat_penyakit_keluarga',
    'riwayat_imunisasi','riwayat_kebiasaan',
    'alergi','keadaan_umum','kulit','kelenjar_getah_bening',
    'mata','hidung','telinga','mulut',
    'tiroid','tumor','lainnya_leher','paru',
    'jantung','hati','limpa','ginjal',
    'lainnya_perut','lengan','tungkai','lainnya_extremitas',
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
					'riwayat_penyakit_dahulu','riwayat_penyakit_keluarga',
	        'riwayat_imunisasi','riwayat_kebiasaan',
	        'alergi','keadaan_umum','kulit','kelenjar_getah_bening',
	        'mata','hidung','telinga','mulut',
	        'tiroid','tumor','lainnya_leher','paru',
	        'jantung','hati','limpa','ginjal',
	        'lainnya_perut','lengan','tungkai','lainnya_extremitas',
					'attribute_internis', 'kesimpulan', 'anjuran',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
      'pernafasan' => Yii::t('fe', 'Pernapasan'),
      'lainnya_leher' => 'Lain-Lain',
      'lainnya_perut' => 'Lain-Lainnya',
      'lainnya_extremitas' => 'Lain-Lainnya',
		];
	}
}
