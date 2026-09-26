<?php

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class ResumeHasilPemeriksaanForm extends DocoBaseModel
{
	protected $xssProtected = [
		'pendaftaran_id',
		'pasien_id',
	];
	
    public $pendaftaran_id;
    public $pasien_id;
    public $resume_hasil_pemeriksaan;
    public $resume_hasil_pemeriksaan_hidden;
    public $tatalaksana_pemeriksaan;
    public $tatalaksana_pemeriksaan_hidden;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[	
                    'resume_hasil_pemeriksaan',
                    'tatalaksana_pemeriksaan',
					'resume_hasil_pemeriksaan_hidden',
					'tatalaksana_pemeriksaan_hidden',
                    'pendaftaran_id', 
                    'pasien_id',
				],
				'safe'
			],
		];
	}

	public function attributeLabels()
	{
        return [
            'resume_hasil_pemeriksaan' => \Yii::t('fe', 'Resume Hasil Pemeriksaan'),
            'tatalaksana_pemeriksaan' => \Yii::t('fe', 'Tatalaksana Pemeriksaan'),
            'pendaftaran_id' => \Yii::t('fe', 'Pendaftaran Id'),
            'pasien_id' => \Yii::t('fe', 'Pasien Id'),
            'resume_hasil_pemeriksaan_hidden' => \Yii::t('fe', 'Resume Hasil Pemeriksaan'),
            'tatalaksana_pemeriksaan_hidden' => \Yii::t('fe', 'Tatalaksana Pemeriksaan'),
        ];
	}
}
?>