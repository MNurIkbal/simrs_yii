<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-09 13:42:40
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-10 11:52:29
 */


namespace Doco\master\models;

use Yii;
use app\models\User;
//use app\models\LoginPemakaiK;


class RubahPasswordForm extends \yii\base\Model
{
	public $nama_pemakai;
	public $kata_sandi;
	public $kata_sandi_baru;
	public $kata_sandi_konfirmasi;		

	public function rules()
	{
		return [
			//[['nama_pemakai','kata_sandi'], 'required'],			
			[['nama_pemakai'], 'string', 'max'=>32],
			['nama_pemakai','checkUser'],		
			['kata_sandi','checkPassword'],	
			[['kata_sandi','kata_sandi_baru','kata_sandi_konfirmasi'], 'string','max'=>200],
			['kata_sandi_konfirmasi','compare','compareAttribute'=>'kata_sandi_baru','message'=>\Yii::t('fe','Kata Sandi Tidak Sesuai')]
		];		
	}
	public function attributeLabels()
	{
		return [
			'nama_pemakai'=>\Yii::t('fe','Nama Pemakai'),
			'kata_sandi'=>\Yii::t('fe','Kata Sandi'),
			'kata_sandi_baru'=>\Yii::t('fe','Kata Sandi Baru'),
			'kata_sandi_konfirmasi'=>\Yii::t('fe','Konfirmasi Kata Sandi Baru'),
		];
	}
	public function checkUser(){
		$user = \app\models\LoginpemakaiK::find()->where(['nama_pemakai'=>$this->nama_pemakai])->one(); 
		if(!$user){
			$this->addError('nama_pemakai',\Yii::t('fe','Nama Pemakai Tidak Terdaftar'));
			return false;
		}			
		return true;

	}
	public function checkPassword(){
		if(!$this->hasErrors()){
			
			$user = \app\models\LoginpemakaiK::find()->where(['nama_pemakai'=>$this->nama_pemakai])->one(); 
			if(!$user || !Yii::$app->getSecurity()->validatePassword($this->kata_sandi, $user->katakunci_pemakai)){
				return $this->addError('kata_sandi',\Yii::t('fe','Kata Sandi Tidak Sesuai'));	
			}
		}

	}
}