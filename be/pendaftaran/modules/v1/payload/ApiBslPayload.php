<?php 

namespace app\modules\v1\payload;

use Yii;

class ApiBslPayload extends \yii\base\Model
{
	public $ruangan_id;

	public function rules()
	{
		return [
			[
                [
                    'ruangan_id',
                ], 
                'required', 'on' => 'get-list-doctor',
            ],
            [['ruangan_id'], 'integer'],
        ];
	}

	public function attributeLabels()
	{
		return [
			'ruangan_id' => 'ID Ruangan',
		];
	}
}