<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-05 08:57:29
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-05 14:59:04
 */

namespace Doco\master\models;

use Yii;

class EsignRegForm extends \yii\base\Model
{
	const SCENARIO_REGIS = 'registrasi';
	const SCENARIO_REENROLL = 'reenroll';

    public $email;
    public $name;
    public $nik;
    public $photo_ktp;

	public $nationality_type;
	public $identity_type;
	public $country_code;

	public $passport_number;
	public $passport_file;
	public $passport_date_expire;

	public $company_supporting_document;
	public $identity_date_expire;

	public function scenarios() {
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_REGIS] = ['nationality_type','email','name','nik','photo_ktp','identity_type','country_code','passport_number','passport_file','passport_date_expire'];
		$scenarios[self::SCENARIO_REENROLL] = ['nationality_type','email','name','nik','photo_ktp','identity_type','country_code','passport_number','passport_file','passport_date_expire'];
		return $scenarios;
	}
	public function rules()
	{
		return [
			[['email'], 'email'],
			[['nationality_type'], 'safe'],
			[['photo_ktp', 'passport_file'], 'file', 'extensions' => 'png, jpg'],
			[['company_supporting_document'], 'file', 'extensions' => 'pdf'],

			[['email','name','nik','photo_ktp',], 'required', 'on' => self::SCENARIO_REGIS],
			[['identity_type','country_code','passport_number','passport_file','passport_date_expire'], 'required', 'on' => self::SCENARIO_REGIS,
				'when' => function($model) {
					return $model->nationality_type == 'WNA';
				}
			],
			[['company_supporting_document','identity_date_expire'], 'required', 'on' => self::SCENARIO_REGIS,
				'when' => function($model) {
					return $model->identity_type == 'KITAS' || $model->identity_type == 'KITAP';
				}
			],

			[['email','name',], 'safe', 'on' => self::SCENARIO_REENROLL,],
			[['nik','photo_ktp','identity_type','country_code','passport_number','passport_file','passport_date_expire'], 'required',
				'on' => self::SCENARIO_REENROLL, 'when' => function($model) {
					return $model->nationality_type == 'WNA';
				}
			],
		];
	}

	public function attributeLabels()
	{
		return [
			'email'=>Yii::t('fe','Email'),
			'name'=>Yii::t('fe','Nama sesuai KTP/KITAS/KITAP'),
			'nik'=>Yii::t('fe','Nomor KTP'),
			'photo_ktp'=>Yii::t('fe','Foto KTP (png/jpg)'),

			'nationality_type'=>Yii::t('fe','Kewarganegaraan'),
			'identity_type'=>Yii::t('fe','Tipe Identitas (WNA)'),
			'country_code'=>Yii::t('fe','Kode Negara'),
			'passport_number'=>Yii::t('fe','Nomor Paspor'),
			'passport_file'=>Yii::t('fe','Foto Paspor (png/jpg)'),
			'passport_date_expire'=>Yii::t('fe','Tanggal Kadarluarsa(expiry date) Paspor'),

			'company_supporting_document'=>Yii::t('fe','Dokumen Pendukung (pdf)'),
			'identity_date_expire'=>Yii::t('fe','Tanggal Kadarluarsa(expiry date) KITAS/KITAP'),
		];
	}

}
