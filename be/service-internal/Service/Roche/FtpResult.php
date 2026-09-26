<?php

namespace Integrasi\Service\Roche;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class FtpResult extends \Integrasi\Contracts\DocoImplement
{

    const EXTENSION = '.pdf';

    public function execute()
    {
        $postData = [
            'his_reg_no' => $this->order_no,
            'image_link' => $this->order_no . self::EXTENSION,
            'update_result' => $this->result,
            'is_complete' => true,
        ];

        $docoRest = Yii::$app->docoRest;
        $docoRest->setToken($this->token);
        $docoRest->setOwner($this->owner);
        try {
            $restToHisRoche = $docoRest->hisRoche->post('api-roche/update-file',[
                'form_params' => $postData
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $restToHisRoche = $e->getResponse();
        }


        // save to log workers
        return json_encode([
            'service' => 'Roche-FtpResult',
            'payload' => $this->attributes,
            'result' => json_decode($restToHisRoche->getBody(), true),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

}