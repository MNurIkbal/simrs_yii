<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-05 08:57:29
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-05 14:59:04
 */

namespace Doco\master\models;

use Yii;

class ProfilUserForm extends \yii\base\Model
{

	public $loginpemakai_id;
	public $nama_pemakai;	
	public $photouser;
	public $jabatan_id;
	

	public function rules()
	{
		return [
			[['loginpemakai_id','nama_pemakai','jabatan_id'], 'required'],
			[['loginpemakai_id','jabatan_id'], 'integer'],
			[['nama_pemakai'], 'string', 'max'=>32],
			[['photouser'], 'file', 'extensions'=>'jpg, png, gif'],
			[['jabatan_id'],'safe']
		];
	}

	public function attributeLabels()
	{
		return [
			'loginpemakai_id'=>Yii::t('fe','ID Login Pengguna'),
			'nama_pemakai'=>Yii::t('fe','Nama Pengguna'),
			'jabatan_id'=>Yii::t('fe','Nama Jabatan'),
			'photouser'=>Yii::t('fe','Foto Pengguna'),
		];
	}

}
