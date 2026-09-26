<?php 

namespace app\modules\v1\payload;

use Yii;

class PendaftaranForm extends \yii\base\Model
{
	public $kamarruangan_id;
	public $ruangan_id;
	public $type;
	public $keyword;

	public function rules()
	{
		return [
			[['kamarruangan_id', 'ruangan_id'], 'integer'],
            [['type', 'keyword'], 'filter', 'filter' => function ($value) {
		         return \yii\helpers\HtmlPurifier::process($value);
		    }],
            [['kamarruangan_id', 'ruangan_id', 'type', 'keyword'], 'safe'],
        ];
	}

	public function attributeLabels()
	{
		return [
			'kamarruangan_id' => 'Ruangan Kamar ID',
			'ruangan_id' => 'Ruangan ID',
			'type' => 'Type',
			'keyword' => 'Keyword',
		];
	}
}