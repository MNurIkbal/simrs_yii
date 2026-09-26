<?php 

namespace app\modules\v1\payload;

use Yii;

class WilayahPayload extends \yii\base\Model
{
	public $propinsi_id;
	public $kabupaten_id;
	public $kecamatan_id;
	public $kelurahan_id;

	public function rules()
	{
		return [
			[
                [
                    'propinsi_id',
                    'kabupaten_id',
                    'kecamatan_id',
                ], 
                'required', 'on' => 'get-kelurahan',
            ],
			[
				[
                    'propinsi_id',
                    'kabupaten_id',
                    'kecamatan_id',
                    'kelurahan_id',
                ], 
                'safe', 'on' => 'default',
            ],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id'], 'integer'],
        ];
	}

	public function attributeLabels()
	{
		return [
			'propinsi_id' => 'Provinsi Id',
			'kabupaten_id' => 'Kabupaten Id',
			'kecamatan_id' => 'Kecamatan Id',
			'kelurahan_id' => 'Kelurahan Id',
		];
	}
}