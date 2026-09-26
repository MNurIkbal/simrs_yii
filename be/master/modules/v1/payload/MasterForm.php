<?php 

namespace app\modules\v1\payload;

use Yii;

class MasterForm extends \yii\base\Model
{
	public $jenisantrian_id;
	public $pendidikan_nama;
	public $pendidikan_namalainnya;
	public $loket_id;

	public function rules()
	{
		return [
			[['jenisantrian_id', 'loket_id'], 'integer'],
			[['pendidikan_nama', 'pendidikan_namalainnya'], 'string'],
            [['jenisantrian_id', 'pendidikan_nama', 'pendidikan_namalainnya'], 'safe'],
        ];
	}

	public function attributeLabels()
	{
		return [
			'jenisantrian_id' => 'Jenis Antrian ID',
			'pendidikan_nama' => 'Pendidikan Nama',
			'pendidikan_namalainnya' => 'Pendidikan Nama Lainnya'
		];
	}
}