<?php

namespace app\components;

use Yii;

class EsignHelpers
{
	public static function previewEsign($options = [])
    {
        $_restRm = Yii::$app->docoRest->rm;
        $get = Yii::$app->request->get();
        if(isset($get['skip_signed'])) {
            return false;
        }
        if(isset($options['name']) || 
        	(isset($options['type']) && (isset($options['pendaftaran_id']) || isset($options['transaksi_id'])))) {
        	$name = isset($options['name']) ? $options['name'] : ($options['type'] . ".pdf");
	        $httpQuery = http_build_query($options);

            try {
    	        $test = $_restRm->get('esign/preview?'.$httpQuery);
                if(reset($test->getHeader('Content-Type')) == 'application/pdf') {
        	        header("Content-type: application/pdf");
        	        header("Content-disposition: inline;filename=" . $name);

        	        echo $test->getBody()->getContents();
        	        exit;
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . "\n" . $e->getTraceAsString());
            }
        }
        Yii::error("Invalid parameters preview esign : " . json_encode($options));
        return false;
    }
}