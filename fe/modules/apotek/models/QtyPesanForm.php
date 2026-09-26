<?php

namespace Doco\apotek\models;

use Yii;

class QtyPesanForm extends \yii\base\Model
{
	public function rules()
    {
    	return [];
    }

    public function validateArrayQty($array)
    {
    	if(is_array($array) && count($array)>0){
	    	foreach ($array as $k_detail => $v_detail) {
	    		$v_detail["value"] = (int) $v_detail["value"];
	    		if($v_detail["value"] == 0 || $v_detail["value"] < 1){
	    			$this->addError($k_detail,'Qty harus lebih dari 1');
	    			return FALSE;
	    		}
	    	}
	    	return TRUE;
	    }
	    return FALSE;
    }
}