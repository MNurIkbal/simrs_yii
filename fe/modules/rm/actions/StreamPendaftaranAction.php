<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\actions;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoSSE;

class StreamPendaftaranAction extends Action
{
	public function run()
    {
        $sse = new DocoSSE();
        $sse->retry(60000);
        $_curl = Yii::$app->docoRest->rm->get('inf-daftar-pasien/get-status-pendaftaran-baru',[
            'query' => [
                'date_request' => date('Y-m-d H:i:s',strtotime('-5 minutes'))
            ]
        ]);
        $_decodeResponse = json_decode($_curl->getBody(),TRUE);
        $response = ArrayHelper::getValue($_decodeResponse,'response',[]);
        $pendaftaran = ArrayHelper::getValue($response,'pendaftaran',[]);

        foreach($pendaftaran as $key => $daftar){
            $pendaftaran[$key]['pendaftaran_id'] = DocoHelpers::encrypt($daftar['pendaftaran_id']);
            $pendaftaran[$key]['pasien_id'] = DocoHelpers::encrypt($daftar['pasien_id']);
        }
        
        $conf = @parse_ini_file('../config/env/.env', true);
        $cookie_key = empty($conf['autoprint']['cookie_key']) ? null : $conf['autoprint']['cookie_key'];

        $sse->message([
            'cookie_key' => $cookie_key,
            'cookie_value' => $_COOKIE[$cookie_key],
            'is_new_registration' => ArrayHelper::getValue($response,'is_new_registration'),
            'pendaftaran' => $pendaftaran, 'autoprint_port' => ArrayHelper::getValue($response,'autoprint_port','3501'),
            'is_print_automatic' => ArrayHelper::getValue($response,'is_print_automatic',false),
            'date' => date('Y-m-d H:i:s')
        ]);

        $sse->flush();
        sleep(1);
        exit();
    }
}