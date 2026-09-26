<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use app\modules\v1\models\KonfigFarmasi;

class StatusDistribusi
{
	public static function getlist()
	{
		$_list =  [
	        'Belum Dikirim' => 'Belum Dikirim',
	        'Sudah Dikirim' => 'Sudah Dikirim',
	        'Diterima' => 'Diterima'
	    ];
	    $konfig = KonfigFarmasi::findOne(1);
	    if($konfig->is_verifpemesanan == TRUE){
	    	$_list = [
	    		'Belum Verifikasi' => 'Belum Verifikasi',
		        'Belum Dikirim' => 'Belum Dikirim',
		        'Sudah Dikirim' => 'Sudah Dikirim',
		        'Diterima' => 'Diterima'
		    ];
	    }
	    return $_list;
	}
}