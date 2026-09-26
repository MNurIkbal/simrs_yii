<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

class ResepturDetail
{
	public function load($inputResepturDetail)
	{
		//
		if(!is_array($inputResepturDetail)) throw new \Exception("Error 2", 1);
        foreach($inputResepturDetail as $_resepturDetail){
        	$resepturDetailPayload = new ResepturDetailPayload;
        	$resepturDetailPayload->attributes = $_resepturDetail;
        	if(!$resepturDetailPayload->validate()) return ['data'=>$resepturDetailPayload->errors];
        }
	}
}