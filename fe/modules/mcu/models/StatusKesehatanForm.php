<?php

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class StatusKesehatanForm extends DocoBaseModel
{
	protected $xssProtected = [
		'nama',
		'status_kesehatan',
		'usia',
		'catatan'
	];
	
    public $tgl_periksa;
	public $resumemedis_id;
	public $nama;
	public $no_rm;
	public $usia;
	public $bagian;
	public $status_kesehatan;
	public $evaluasi;
	public $catatan;

    public $pendaftaran_id;
    public $pasien_id;
    public $ruanganterakhir_id;
    public $pegawai_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['tgl_periksa'], 'required'],
			[
				[	
                    'resumemedis_id', 
					'tgl_periksa', 'nama','no_rm','usia', 'bagian', 'catatan', 'status_kesehatan', 'evaluasi',
                    'pendaftaran_id', 'pasien_id', 'ruanganterakhir_id', 'pegawai_id',
				],
				'safe'
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
            'tgl_periksa' => Yii::t('fe', 'Tanggal Pemeriksaan'),
            'nama' => Yii::t('fe', 'Nama'),
            'no_rm' => Yii::t('fe', 'No Rekam Medik'),
            'usia' => Yii::t('fe', 'Usia'),
            'bagian' => Yii::t('fe', 'Bagian'),
            'catatan' => Yii::t('fe', 'Catatan'),
            'status_kesehatan' => Yii::t('fe', 'Status Kelaikan'),
            'evaluasi' => Yii::t('fe', 'Evaluasi Ulang'),
		];
	}
}
?>