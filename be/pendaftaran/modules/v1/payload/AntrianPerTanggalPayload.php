<?php 

namespace app\modules\v1\payload;

use Yii;

class AntrianPerTanggalPayload extends \yii\base\Model
{
	public $tanggalawal;
	public $tanggalakhir;

	public function rules()
	{
		$oneWeekAgo = date('Y-m-d', strtotime('-7days'));
		return [
            [['tanggalawal', 'tanggalakhir'], 'date', 'format' => 'php:Y-m-d'],
			['tanggalakhir', 'compare', 'compareAttribute' => 'tanggalawal', 'operator' => '>=', 'message'=>'{attribute} Harus lebih besar dari tanggal awal.'],
			[['tanggalawal', 'tanggalakhir'], 'compare', 'compareValue' => $oneWeekAgo, 'operator' => '>=', 'message'=>'{attribute} Tidak boleh lebih dari 7 hari yang lalu.'],
			['tanggalawal', 'rangeDateInAWeek', 'params' => ['end_date' => 'tanggalakhir']],
            [['tanggalawal', 'tanggalakhir'], 'safe'],
        ];
	}

	public function attributeLabels()
	{
		return [
			'tanggalawal' => 'Tanggal Awal',
			'tanggalakhir' => 'Tanggal Akhir',
		];
	}

	public function rangeDateInAWeek($attribute, $params)
	{
		$attribute_datetime = new \DateTime($this->{$attribute});
		$tanggakakhir_datetime = new \DateTime($this->{$params['end_date']});
		$interval = $attribute_datetime->diff($tanggakakhir_datetime);
		$diff = $interval->format('%a');
		if ($diff > 7) {
			$this->addError($attribute,'Range tanggal awal dan tanggal akhir tidak boleh melebihi 7 hari');
			$this->addError($params['end_date'],'Range tanggal awal dan tanggal akhir tidak boleh melebihi 7 hari');
		}
	}
}