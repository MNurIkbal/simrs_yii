<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanFisikDefaultForm extends \yii\base\Model
{
	public $pendaftaran_id;
	public $berat_badan;
	public $tinggi_badan;
	public $td_sistolik;
	public $td_diastolik;
	public $pernafasan;
	public $detak_nadi;
	public $suhu;
	public $pemeriksaan_lainnya;
	public $imt;
	public $kategori_bb;
    public $td_kategori;
    
    // kulit rambut
    public $kulit;
    public $limfonodi;
    public $kepala_rambut;
    public $tato;
    public $tindik;
    public $note_kulit;
    public $note_limfonodi;
    public $note_tato;
    public $note_tindik;


    // mata
    public $visus_tanpa_kaca_mata_kanan;
    public $visus_tanpa_kaca_mata_kiri;
    public $visus_dengan_kaca_mata_kanan;
    public $visus_dengan_kaca_mata_kiri;
    public $buta_warna;
    public $kelopak_mata;
    public $kanjungtiva;
    public $sklera;
    public $pupil;
    public $bola_mata;
    public $gerakan_bola_mata;
    public $lensa_mata;
    public $koreksi_kanan;
    public $koreksi_kiri;
    public $note_kelopak_mata;
    public $note_kanjungtiva;
    public $note_gerakan_bola_mata;

    // telinga
    public $kelainan_daun_telinga;
    public $serumen_prop;
    public $liang_telinga_luar;
    public $membran_timpani;
    public $note_kelainan_daun_telinga;
    public $note_serumen_prop;
    public $note_membran_timpani;

    // hidung
    public $hidung;
    public $note_hidung;

    // mulut dan tenggorokan
    public $gigi;
    public $faring;
    public $tonsil;
    public $tonsil_text;
    public $kelenjar_tiroid;
    public $note_kelenjar_tiroid;

    // paru paru
    public $inspeksi_paru;
    public $palpasi_paru;
    public $perkusi_paru;
    public $auskultasi_paru;
    public $note_inspeksi_paru;
    public $note_palpasi_paru;
    public $note_perkusi_paru;
    public $note_auskultasi_paru;

    // jantung
    public $ictus_cordis;
    public $batas_jantung;
    public $irama;
    public $bunyi_jantung;
    public $note_bunyi_jantung;

    // abdomen
    public $inspeksi_abdomen;
    public $auskultasi_abdomen;
    public $perkusi_abdomen;
    public $palpasi_abdomen;
    public $rectal_toucher;
    public $genital;
    public $note_inspeksi_abdomen;
    public $note_auskultasi_abdomen;
    public $note_perkusi_abdomen;
    public $note_palpasi_abdomen;
    public $note_rectal_toucher;
    public $note_genital;

    // MUSKULOSKELETAL
    public $deformitas;
    public $fungsi_motorik;
    public $fungsi_sensorik;
    public $refleks_fisiolog;
    public $refleks_patologis;
    public $note_deformitas;
    public $note_fungsi_motorik;
    public $note_fungsi_sensorik;
    public $note_refleks_fisiolog;
    public $note_refleks_patologis;
	
    public $deformitas_kanan_atas;
	public $deformitas_kiri_atas;
    public $deformitas_kanan_bawah;
	public $deformitas_kiri_bawah;
    public $note_deformitas_kanan_atas;
    public $note_deformitas_kanan_bawah;
    public $note_deformitas_kiri_atas;
    public $note_deformitas_kiri_bawah;

    public $fungsi_motorik_kanan_atas;
	public $fungsi_motorik_kiri_atas;
    public $fungsi_motorik_kanan_bawah;
	public $fungsi_motorik_kiri_bawah;
    public $note_fungsi_motorik_kanan_atas;
    public $note_fungsi_motorik_kanan_bawah;
    public $note_fungsi_motorik_kiri_atas;
    public $note_fungsi_motorik_kiri_bawah;

    public $fungsi_sensorik_kanan_atas;
	public $fungsi_sensorik_kiri_atas;
    public $fungsi_sensorik_kanan_bawah;
	public $fungsi_sensorik_kiri_bawah;
    public $note_fungsi_sensorik_kanan_atas;
    public $note_fungsi_sensorik_kanan_bawah;
    public $note_fungsi_sensorik_kiri_atas;
    public $note_fungsi_sensorik_kiri_bawah;

    public $refleks_fisiologis_kanan_atas;
	public $refleks_fisiologis_kiri_atas;
    public $refleks_fisiologis_kanan_bawah;
	public $refleks_fisiologis_kiri_bawah;
    public $note_refleks_fisiologis_kanan_atas;
    public $note_refleks_fisiologis_kanan_bawah;
    public $note_refleks_fisiologis_kiri_atas;
    public $note_refleks_fisiologis_kiri_bawah;

    public $refleks_patologis_kanan_atas;
	public $refleks_patologis_kiri_atas;
    public $refleks_patologis_kanan_bawah;
	public $refleks_patologis_kiri_bawah;
    public $note_refleks_patologis_kanan_atas;
    public $note_refleks_patologis_kanan_bawah;
    public $note_refleks_patologis_kiri_atas;
    public $note_refleks_patologis_kiri_bawah;

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
					'detak_nadi','suhu','pemeriksaan_lainnya', 'imt', 'kategori_bb', 'td_kategori', 
                    'kulit', 'limfonodi', 'kepala_rambut', 'visus_tanpa_kaca_mata_kanan', 
                    'visus_tanpa_kaca_mata_kiri', 'visus_dengan_kaca_mata_kanan', 
                    'visus_dengan_kaca_mata_kiri', 'buta_warna', 'kelopak_mata', 'kanjungtiva', 
                    'sklera', 'pupil', 'bola_mata', 'gerakan_bola_mata', 'lensa_mata', 
                    'kelainan_daun_telinga', 'serumen_prop', 'liang_telinga_luar', 
                    'membran_timpani', 'hidung', 'gigi', 'faring', 'tonsil', 'tonsil_text', 
                    'kelenjar_tiroid', 'inspeksi_paru', 'palpasi_paru', 'perkusi_paru', 
                    'auskultasi_paru', 'ictus_cordis', 'batas_jantung', 'irama', 'inspeksi_abdomen', 
                    'auskultasi_abdomen', 'perkusi_abdomen', 'palpasi_abdomen', 'rectal_toucher', 
                    'genital', 'deformitas', 'fungsi_motorik', 'fungsi_sensorik', 
                    'refleks_fisiolog', 'refleks_patologis','note_kulit','note_limfonodi',
                    'tato','tindik','note_tato','note_tindik','koreksi_kanan','koreksi_kiri',
                    'note_kelopak_mata','note_kanjungtiva','note_gerakan_bola_mata',
                    'note_kelainan_daun_telinga','note_serumen_prop','note_membran_timpani',
                    'note_hidung','note_kelenjar_tiroid','note_inspeksi_paru','note_palpasi_paru',
                    'note_perkusi_paru','note_auskultasi_paru','bunyi_jantung','note_bunyi_jantung',
                    'note_inspeksi_abdomen','note_auskultasi_abdomen','note_perkusi_abdomen',
                    'note_palpasi_abdomen','note_rectal_toucher','note_genital','note_deformitas',
                    'note_fungsi_motorik','note_fungsi_sensorik','note_refleks_fisiolog',
                    'note_refleks_patologis',
                    'deformitas_kanan_atas', 'deformitas_kiri_atas', 'deformitas_kanan_bawah', 'deformitas_kiri_bawah',
                    'note_deformitas_kanan_atas', 'note_deformitas_kanan_bawah', 'note_deformitas_kiri_atas', 'note_deformitas_kiri_bawah',
                    'fungsi_motorik_kanan_atas', 'fungsi_motorik_kiri_atas', 'fungsi_motorik_kanan_bawah', 'fungsi_motorik_kiri_bawah',
                    'note_fungsi_motorik_kanan_atas', 'note_fungsi_motorik_kanan_bawah', 'note_fungsi_motorik_kiri_atas', 'note_fungsi_motorik_kiri_bawah',
                    'fungsi_sensorik_kanan_atas', 'fungsi_sensorik_kiri_atas', 'fungsi_sensorik_kanan_bawah', 'fungsi_sensorik_kiri_bawah',
                    'note_fungsi_sensorik_kanan_atas', 'note_fungsi_sensorik_kanan_bawah', 'note_fungsi_sensorik_kiri_atas', 'note_fungsi_sensorik_kiri_bawah',
                    'refleks_fisiologis_kanan_atas', 'refleks_fisiologis_kiri_atas', 'refleks_fisiologis_kanan_bawah', 'refleks_fisiologis_kiri_bawah',
                    'note_refleks_fisiologis_kanan_atas', 'note_refleks_fisiologis_kanan_bawah', 'note_refleks_fisiologis_kiri_atas', 'note_refleks_fisiologis_kiri_bawah',
                    'refleks_patologis_kanan_atas', 'refleks_patologis_kiri_atas', 'refleks_patologis_kanan_bawah', 'refleks_patologis_kiri_bawah',
                    'note_refleks_patologis_kanan_atas', 'note_refleks_patologis_kanan_bawah', 'note_refleks_patologis_kiri_atas', 'note_refleks_patologis_kiri_bawah',
                    'pemeriksaan_lainnya'
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
            'refleks_fisiolog' => Yii::t('fe', 'Refleks Fisiologis'),
		];
	}
}
