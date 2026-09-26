<?php

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class RiwayatPenyakitForm extends DocoBaseModel
{
	public $pendaftaran_id;
	public $pasien_id;
	public $keluhan;
	public $riwayat_diderita;
	public $riwayat_diderita_catatan;
	public $riwayat_alergi;
	public $riwayat_alergi_catatan;
	public $riwayat_dirawat_rs;
	public $riwayat_dirawat_rs_catatan;
	public $riwayat_operasi;
	public $riwayat_operasi_catatan;
	public $riwayat_imunisasi;
	public $riwayat_imunisasi_catatan;

	public $menstruasi;
	public $riwayat_kontrasepsi;
	public $riwayat_melahirkan;
	public $riwayat_keguguran;
	public $sedang_hamil;
	public $riwayat_pap_smear;
	public $riwayat_penyakit_keluarga;

	public $rokok;
	public $alkohol;
	public $kopi;
	public $olahraga;
	public $diet;
	public $tidur;
	public $obat_rutin;

	protected $xssProtected = [
        'keluhan',
        'riwayat_diderita_catatan',
        'riwayat_alergi_catatan',
        'riwayat_dirawat_rs_catatan',
        'riwayat_operasi_catatan',
        'riwayat_imunisasi_catatan',
        'menstruasi',
        'riwayat_kontrasepsi',
        'riwayat_melahirkan',
        'riwayat_keguguran',
        'sedang_hamil',
        'riwayat_pap_smear',
        'riwayat_penyakit_keluarga',
        'olahraga',
        'obat_rutin',
    ];

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				['pendaftaran_id', 'pasien_id', 'keluhan', 'riwayat_diderita', 'riwayat_alergi', 
				'riwayat_diderita_catatan', 'riwayat_alergi_catatan','riwayat_dirawat_rs',
				'riwayat_dirawat_rs_catatan', 'riwayat_operasi', 'riwayat_operasi_catatan', 
				'riwayat_imunisasi', 'riwayat_imunisasi_catatan', 'menstruasi', 'riwayat_kontrasepsi', 
				'riwayat_melahirkan', 'riwayat_keguguran', 'sedang_hamil', 
				'riwayat_penyakit_keluarga', 'riwayat_pap_smear', 'rokok', 'alkohol', 
				'kopi', 'olahraga', 'diet', 'tidur', 'obat_rutin'
				],
				'safe'
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'keluhan' => Yii::t('fe', 'Keluhan saat ini'),
            'riwayat_diderita' => Yii::t('fe', 'Riwayat penyakit yang pernah di derita'),
            'riwayat_dirawat_rs' => Yii::t('fe', 'Riwayat dirawat di RS'),
            'riwayat_pap_smear' => Yii::t('fe', "Riwayat Pap's Smear"),
            'olahraga' => Yii::t('fe', 'Olah Raga'),
		];
	}
}
?>