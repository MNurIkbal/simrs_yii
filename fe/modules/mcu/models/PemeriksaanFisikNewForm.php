<?php
namespace app\modules\mcu\models;

use Yii;

class PemeriksaanFisikNewForm extends \yii\base\Model
{
    // keadaan umum
    public $pendaftaran_id;
    public $kesadaran_umum_batas_normal;
    public $kesadaran;
    public $kontak;
    public $gangguan_berjalan;
    public $sakit_saat_berjalan;
    public $postur;
    public $keterangan_keadaan_umum;
    // Kelenjar getah bening
    public $kelenjar_getah_bening_umum_batas_normal;
    public $leher;
    public $aksilla;
    public $inguinal;
    public $lainnya_kelenjar_getah_bening;
    public $note_lainnya_kelenjar_getah_bening;
    public $keterangan_kelenjar_getah_bening;
    // Kepala
    public $kepala_umum_batas_normal;
    public $bentuk_wajah;
    public $kulit_kepala;
    public $rambut;
    public $keterangan_kepala;
    // saraf
    public $saraf_umum_batas_normal;
    public $motorik;
    public $sensorik;
    public $ref_fisologis;
    public $ref_patologis;
    public $n_facialis;
    // tes provokasi radiks cervical
    public $cervical_umum_batas_normal;
    public $tes_spurling;
    public $tes_distraksi;
    public $keterangan_cervacal;
    // tes provokasi radiks lumbar
    public $lumbar_umum_batas_normal;
    public $tes_lasegue;
    public $tes_braggard;
    public $keterangan_lumbar;
    // mata
    public $mata_umum_batas_normal;
    public $persepsi_mata;
    public $persepsi_mata_note;
    public $stabisnus;
    public $kelopak_mata_kanan;
    public $kelopak_mata_kiri;
    public $bulu_mata_kanan;
    public $bulu_mata_kiri;
    public $konjungtiva_kanan;
    public $konjungtiva_kiri;
    public $note_konjungtiva_kanan;
    public $note_konjungtiva_kiri;
    public $sklera_kanan;
    public $sklera_kiri;
    public $kornea_kanan;
    public $kornea_kiri;
    public $lensa_kanan;
    public $lensa_kiri;
    public $pupil_kanan;
    public $pupil_kiri;
    public $direk_kanan;
    public $direk_kiri;
    public $indirek_kanan;
    public $indirek_kiri;
    public $lapang_pandang_kanan;
    public $diameter;
    public $tanpa_koreksi_OD;
    public $tanpa_koreksi_OS;
    public $tanpa_koreksi_ODS_jauh;
    public $tanpa_koreksi_ODS_dekat;
    public $dengan_koreksi_OD;
    public $dengan_koreksi_OS;
    public $dengan_koreksi_ODS_jauh;
    public $dengan_koreksi_ODS_dekat;
    public $keterangan_mata;
    // hidung
    public $hidung_umum_batas_normal;
    public $metus_nasi;
    public $septum_nasi;
    public $konka_nasal;
    public $nyeri_tekan_sinus;
    public $penciuman;
    public $keterangan_hidung;
    // tenggorokan
    public $tenggorokan_umum_batas_normal;
    public $pharinx;
    public $tonsil;
    public $ukuran;
    public $palatum;
    public $keterangan_tenggorokan;
    // mulut
    public $mulut_umum_batas_normal;
    public $bibir;
    public $lidah;
    public $gusi;
    public $mukosa;
    // leher
    public $leher_umum_batas_normal;
    public $otot_leher;
    public $kelenjar_thyroid;
    public $jegular_vein_pressure;
    public $trakea;
    public $keterangan_leher;
    // torax
    public $torax_umum_batas_normal;
    public $bentuk_torax;
    public $note_bentuk_torax;
    public $mamae;
    // jantung
    public $jantung_umum_batas_normal;
    public $bunyi_jantung;
    public $note_bunyi_jantung;
    public $ictus_cordis;
    public $batas_kiri_jantung;
    public $note_batas_kiri_jantung;
    public $keterangan_jantung;
    // extremitas dan vertebrata
    public $extremitas_umum_batas_normal;
    public $range_of_motion;
    public $cervical;
    public $note_cervical;
    public $extremitas_atas;
    public $lumbar;
    public $extremitas_bawah;
    public $manual_muscle_test;
    public $extremitas_atas_tes;
    public $extremitas_bawah_tes;
    public $note_extremitas_atas;
    public $note_lumbar;
    public $note_extremitas_bawah;
    public $note_extremitas_atas_tes;
    public $note_extremitas_bawah_tes;
    public $phallen;
    public $reverse_phallen;
    public $tinnel_sign;
    public $edema_tungkai;
    public $keterangan_extremitas;
    // paru paru
    public $paru_umum_batas_normal;
    public $pergerakan;
    public $perkusi_kanan;
    public $perkusi_kiri;
    public $bunyi_napas_kanan;
    public $bunyi_napas_kiri;
    public $membran_timpani_paru_kanan;
    public $membran_timpani_paru_kiri;
    public $pendengaran_paru_kanan;
    public $pendengaran_paru_kiri;
    public $keterangan_paru;
    // telinga
    public $telinga_umum_batas_normal;
    public $liang_telinga_kanan;
    public $liang_telinga_kiri;
    public $serumen_kanan;
    public $serumen_kiri;
    public $membran_timpani_telinga_kanan;
    public $membran_timpani_telinga_kiri;
    public $pendengaran_telinga_kanan;
    public $pendengaran_telinga_kiri;
    public $keterangan_telinga;
    // kulit
    public $kulit_umum_batas_normal;
    public $distribusi;
    public $lesi;
    public $karakteristik;
    public $efloresensi;
    // abdomen
    public $abdomen_umum_batas_normal;
    public $inspeksi;
    public $auskultasi_abdomen;
    public $perkusi;
    public $palpasi;
    public $hati;
    public $limfa;
    public $nyeri_tekan;
    public $nyeri_ketok_CVA_kanan;
    public $nyeri_ketok_CVA_kiri;
    public $keterangan_abdomen;
    //pemeriksaan fisik
    public $berat_badan;
    public $tinggi_badan;
    public $imt;
    public $detak_nadi;
    public $pernafasan;
    public $suhu;
    public $td_sistolik;
    public $td_diastolik;
    public $pupil_isokor;
    public $pupil_diameter;
    public $lapang_pandang;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[	'pendaftaran_id',
                    'kesadaran_umum_batas_normal', 'kesadaran', 'kontak', 'gangguan_berjalan','sakit_saat_berjalan','postur','keterangan_keadaan_umum', //keadaan umum
                    'kelenjar_getah_bening_umum_batas_normal','leher','aksilla','inguinal','lainnya_kelenjar_getah_bening','note_lainnya_kelenjar_getah_bening','keterangan_kelenjar_getah_bening', //kelenjar getah bening
                    'kepala_umum_batas_normal','bentuk_wajah','kulit_kepala','rambut','keterangan_kepala', //kepala
                    'saraf_umum_batas_normal','motorik','sensorik','ref_fisologis','ref_patologis','n_facialis', //saraf
                    'cervical_umum_batas_normal','tes_spurling','tes_distraksi','keterangan_cervacal', // es provokasi radiks cervical
                    'lumbar_umum_batas_normal','tes_lasegue','tes_braggard','keterangan_lumbar', //tes provokasi radiks lumbar
                    'mata_umum_batas_normal','persepsi_mata','persepsi_mata_note','stabisnus','kelopak_mata_kanan','kelopak_mata_kiri','bulu_mata_kanan','bulu_mata_kiri','konjungtiva_kanan','konjungtiva_kiri', //mata
                    'note_konjungtiva_kanan','note_konjungtiva_kiri','sklera_kanan','sklera_kiri','kornea_kanan','kornea_kiri','lensa_kanan','lensa_kiri','pupil_kanan','pupil_kiri', //mata
                    'direk_kanan','direk_kiri','indirek_kanan','indirek_kiri','lapang_pandang_kanan','diameter','tanpa_koreksi_OD','tanpa_koreksi_OS','tanpa_koreksi_ODS_jauh', //mata
                    'tanpa_koreksi_ODS_dekat','dengan_koreksi_OD','dengan_koreksi_OS','dengan_koreksi_ODS_jauh','dengan_koreksi_ODS_dekat','keterangan_mata', // mata
                    'hidung_umum_batas_normal','metus_nasi','septum_nasi','konka_nasal','nyeri_tekan_sinus','metus_nasi','septum_nasi','konka_nasal','nyeri_tekan_sinus','penciuman','keterangan_hidung', //hidung
                    'tenggorokan_umum_batas_normal','pharinx','tonsil','ukuran','palatum','keterangan_tenggorokan', //tenggorokan
                    'mulut_umum_batas_normal','bibir','lidah','gusi','mukosa', //mulut
                    'leher_umum_batas_normal','otot_leher','kelenjar_thyroid','jegular_vein_pressure','trakea','keterangan_leher', //leher
                    'torax_umum_batas_normal','bentuk_torax','note_bentuk_torax','mamae', //torax
                    'jantung_umum_batas_normal','bunyi_jantung','note_bunyi_jantung','ictus_cordis','batas_kiri_jantung','note_batas_kiri_jantung','keterangan_jantung', //jantung
                    'extremitas_umum_batas_normal','range_of_motion','cervical','note_cervical','extremitas_atas','lumbar','extremitas_bawah','manual_muscle_test','extremitas_atas_tes','note_extremitas_atas','note_lumbar','note_extremitas_bawah', //extremitas dan vertebrata
                    'note_extremitas_atas_tes','note_extremitas_bawah_tes','extremitas_bawah_tes','phallen','reverse_phallen','tinnel_sign','edema_tungkai','keterangan_extremitas', //extremitas dan vertebrata
                    'paru_umum_batas_normal','pergerakan','perkusi_kanan','perkusi_kiri','bunyi_napas_kanan','bunyi_napas_kiri','membran_timpani_paru_kanan','membran_timpani_paru_kiri','pendengaran_paru_kanan','pendengaran_paru_kiri','keterangan_paru', //paru -paru
                    'telinga_umum_batas_normal','liang_telinga_kanan','liang_telinga_kiri','serumen_kanan','serumen_kiri','membran_timpani_telinga_kanan','membran_timpani_telinga_kiri','pendengaran_telinga_kanan','pendengaran_telinga_kiri','keterangan_telinga', //telinga
                    'kulit_umum_batas_normal','distribusi','lesi','karakteristik','efloresensi', //kulit
                    'inspeksi','auskultasi_abdomen','perkusi','palpasi','rectal_toucher','genital','hati','limfa','nyeri_tekan','nyeri_ketok_CVA_kanan','nyeri_ketok_CVA_kiri','keterangan_abdomen','abdomen_umum_batas_normal', //abdomen
                    'berat_badan','tinggi_badan','imt','detak_nadi','pernafasan','suhu','td_sistolik','td_diastolik', // pemeriksaan fisik
                    'pupil_isokor', 'pupil_diameter', 'lapang_pandang',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
		return [
            'kesadaran_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'kelenjar_getah_bening_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'kepala_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'saraf_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'mata_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'tenggorokan_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'hidung_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'telinga_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'extremitas_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'lumbar_umum_batas_normal' => Yii::t('fe', 'Tidak Diperiksa'),
            'jantung_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'cervical_umum_batas_normal' => Yii::t('fe', 'Tidak Diperiksa'),
            'leher_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'torax_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'mulutumum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'paru_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'abdomen_umum_batas_normal' => Yii::t('fe', 'Dalam Batas Normal'),
            'sakit_saat_berjalan' => Yii::t('fe', 'Tampak Kesakitan Saat Berjalan'),
            'note_lainnya_kelenjar_getah_bening' => Yii::t('fe', 'Lainnya'),
            'keterangan_keadaan_umum' => Yii::t('fe', 'Keterangan'),
            'keterangan_kelenjar_getah_bening' => Yii::t('fe', 'Keterangan'),
            'keterangan_kepala' => Yii::t('fe', 'Keterangan'),
            'keterangan_cervacal' => Yii::t('fe', 'Keterangan'),
            'keterangan_lumbar' => Yii::t('fe', 'Keterangan'),
            'keterangan_tenggorokan' => Yii::t('fe', 'Keterangan'),
            'keterangan_leher' => Yii::t('fe', 'Keterangan'),
            'keterangan_hidung' => Yii::t('fe', 'Keterangan'),
            'keterangan_extremitas' => Yii::t('fe', 'Keterangan'),
            'keterangan_jantung' => Yii::t('fe', 'Keterangan'),
            'keterangan_telinga' => Yii::t('fe', 'Keterangan'),
            'keterangan_abdomen' => Yii::t('fe', 'Keterangan'),
            'keterangan_paru' => Yii::t('fe', 'Keterangan'),
            'ref_fisologis' => Yii::t('fe', 'Ref.Fisologis'),
            'ref_patologis' => Yii::t('fe', 'Ref.Potologis'),
            'n_facialis' => Yii::t('fe', 'N.Facialis'),
            'tes_spurling' => Yii::t('fe', 'Tes Spurling (Modified)'),
            'extremitas_atas_tes' => Yii::t('fe', 'Ekstremitas Atas'),
            'extremitas_atas' => Yii::t('fe', 'Ekstremitas Atas'),
            'extremitas_bawah_tes' => Yii::t('fe', 'Ekstremitas Bawah'),
            'extremitas_bawah' => Yii::t('fe', 'Ekstremitas Bawah'),
            'auskultasi_abdomen' => Yii::t('fe', 'Auskultasi (Bising Usus)'),
            'keterangan_mata' => Yii::t('fe', 'Keterangan'),
            'bentuk_torax' => Yii::t('fe', 'Bentuk'),
            'metus_nasi' => Yii::t('fe', 'Meatus Nasi'),
		];
	}
}
