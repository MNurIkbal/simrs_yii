<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanMcuForm extends \yii\base\Model
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

	// keperluan semua form
	public $keluhan;
	public $kesimpulan;
	public $saran;

	// keperluan form KARDIOLOGI
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
	
	// keperluan form THT
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

	// keperluan form MATA
	public $visus_kanan;
	public $visus_kiri;
	public $koreksi_kanan;
	public $koreksi_kiri;
	public $adisi_kanan;
	public $adisi_kiri;
	public $gerakan_mata_kanan;
	public $gerakan_mata_kiri;
	public $kedudukan_kanan;
	public $kedudukan_kiri;
	public $palpebra_kanan;
	public $palpebra_kiri;
	public $conjuctiva_kanan;
	public $conjuctiva_kiri;
	public $cornea_kanan;
	public $cornea_kiri;
	public $coa_kanan;
	public $coa_kiri;
	public $pupil_kanan;
	public $pupil_kiri;
	public $iris_kanan;
	public $iris_kiri;
	public $lensa_kanan;
	public $lensa_kiri;
	public $vitreous_kanan;
	public $vitreous_kiri;
	public $fundus_kanan;
	public $fundus_kiri;
	public $tio_kanan;
	public $tio_kiri;
	public $test_buta_warna;
	public $lapang_pandang;
	public $anjuran;

	public $pemeriksaanspesialismcu_id;

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
					'detak_nadi', 'pernafasan', 'suhu', 'keluhan', 'daun_telinga_kanan', 
					'daun_telinga_kiri', 'hidung', 'tenggorokan', 'nasoendoskopi', 
					'leher', 'liang_telinga_kanan', 'liang_telinga_kiri', 
					'membran_tympani_kanan', 'membran_tympani_kiri', 
					'audiogram_kanan', 'audiogram_kiri', 'visus_kanan', 'visus_kiri', 
					'koreksi_kanan', 'koreksi_kiri', 'adisi_kanan', 'adisi_kiri', 
					'gerakan_mata_kanan', 'gerakan_mata_kiri', 
					'kedudukan_kanan', 'kedudukan_kiri', 'palpebra_kanan', 'palpebra_kiri', 
					'conjuctiva_kanan', 'conjuctiva_kiri', 'cornea_kanan', 'cornea_kiri', 
					'coa_kanan', 'coa_kiri', 'pupil_kanan', 'pupil_kiri', 'iris_kanan', 
					'iris_kiri', 'lensa_kanan', 'lensa_kiri', 'vitreous_kanan', 'vitreous_kiri',
					'fundus_kanan', 'fundus_kiri', 'tio_kanan', 'tio_kiri', 'test_buta_warna', 
					'lapang_pandang', 'anjuran', 'td_diastolik',
					'kesimpulan', 'tumor', 'jvp', 'paru', 'jantung', 'hati', 'limpa', 'lainnya', 
					'edema', 'rontgen_thorax', 'ekg', 'echocardiografi', 
					'pemeriksaanspesialismcu_id', 'saran', 
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
