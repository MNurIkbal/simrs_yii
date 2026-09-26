<?php

/**
 * @Author: Rizal
 * @Date:   2018-07-31 17:43:05
 */

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class ResumeMedisForm extends DocoBaseModel
{
	protected $xssProtected = [
		'ikhtisar_singkat',
		'kesimpulan',
		'saran',
		'catatan'
	];
	
	public $resumemedis_id;
	public $pendaftaran_id;
	public $pasien_id;
	public $pegawai_id;
	public $pegawai_nama;
	public $ruanganterakhir_id;
	public $pemeriksaanfisik_id;
	public $tgl_resume;
	public $ikhtisar_singkat;
	public $pengobatan_sementara;
	public $diagnosaawal_id;
	public $diagnosautama_id;
	public $saran;
	public $kesimpulan;
	public $catatan;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pendaftaran_id','pasien_id','ikhtisar_singkat', 'kesimpulan'], 'required', 'on' => 'pemeriksaan-mcu'],
			[
				[	'resumemedis_id', 
					'pendaftaran_id','pasien_id','pegawai_id' ,'pegawai_nama', 'ruanganterakhir_id',
					'pemeriksaanfisik_id','tgl_resume','ikhtisar_singkat','pengobatan_sementara',
					'diagnosaawal_id','diagnosautama_id','saran','kesimpulan', 'catatan'
				],
				'safe'
			],
			[['pendaftaran_id','pasien_id','ruanganterakhir_id'], 'required', 'on' => 'default']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
            'saran' => Yii::t('fe', 'Saran'),
            'pegawai_id' => Yii::t('fe', 'Dokter'),
            'pegawai_nama' => Yii::t('fe', 'Dokter'),
            'ikhtisar_singkat' => Yii::t('fe', 'Kesimpulan'),
            'kesimpulan' => Yii::t('fe', 'Hasil'),
            'catatan' => Yii::t('fe', 'Catatan'),
		];
	}
}
?>