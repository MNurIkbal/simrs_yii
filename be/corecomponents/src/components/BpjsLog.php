<?php
/**
 * @Author: Audris EU
 * @Date:   2022-07-05
 */

namespace Doco\components;

use Yii;
use yii\helpers\ArrayHelper;

class BpjsLog extends \yii\base\Component
{
    public static function saveLogBpjs($url,$header, $request, $response)
    {
		if(is_array($header)) {
			$header = json_encode($header);
		}
		if(is_array($request)) {
			$request = json_encode($request);
		}
		if(is_array($response)) {
			$response = json_encode($response);
		}
        Yii::$app->db->createCommand()->insert('logbpjs_r', [
            'url' => $url,
            'header' => $header,
            'request' => $request,
            'response' => $response,
            'created_date' => date('Y-m-d H:i:s'),
        ])->execute();

        $responseDecode = json_decode($response,true);
        $codeBPJS = ArrayHelper::getValue($responseDecode,'metaData.code');
        if(isset($codeBPJS) && $codeBPJS != 200){
            Yii::warning([
                'url' => $url,
                'header' => $header,
                'request' => $request,
                'response' => $response,
                'created_date' => date('Y-m-d H:i:s'),
            ],'BPJS');
        }
    }
}