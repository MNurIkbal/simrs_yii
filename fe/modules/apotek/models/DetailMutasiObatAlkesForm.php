<?php

namespace app\modules\apotek\models;

use Yii;

class DetailMutasiObatAlkesForm extends \yii\base\Model
{
	public function rules()
	{
		return [];
	}

	public function check($detail)
	{
		if(!is_array($detail))
			return false;
		if(count($detail) <= 0)
			return false;
		$isThereQty = false;
		foreach ($detail as $pesanobatdetail_id => $qty_kirim) {
			if($qty_kirim < 0){
				$this->addError($pesanobatdetail_id,'Qty Salah');
				return false;
			}else if($qty_kirim != 0 && $qty_kirim != ''){
				$isThereQty = true;
			}
		}

		return $isThereQty;
	}

	public function clearNull($detail)
	{
		$val = [];
		foreach ($detail as $pesanobatdetail_id => $qty_kirim) {
			if($qty_kirim != 0 && $qty_kirim != ''){
				$val[$pesanobatdetail_id] = $qty_kirim;
			}
		}
		return $val;
	}
}