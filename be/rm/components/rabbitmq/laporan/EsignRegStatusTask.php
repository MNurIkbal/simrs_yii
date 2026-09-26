<?php

namespace app\components\rabbitmq\laporan;

use app\modules\v1\models\DokumenSign;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;

use Doco\Notifications\RmNotification;

use Doco\rabbitmq\task\BaseTask;
use Doco\rabbitmq\RabbitBgProcess;

use Doco\Services\Esign\TilakaService;
use Doco\models\Pegawai;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

use Yii;

class EsignRegStatusTask extends BaseTask
{
    public function processFlow($parameters)
    {
        $pegawai_id = $parameters['pegawai_id'];
        $registration_id = $parameters['registration_id'];
        try {
            $registration_result = TilakaService::registrationResult($registration_id);
            $data = Pegawai::find()
                ->select([
                    'additional_esign_data'
                ])
                ->where(['pegawai_id' => $pegawai_id])
                ->asArray()->one();
            $additional_esign_data = json_decode($data['additional_esign_data'], true);
            $additional_esign_data['registration_result'] = $registration_result;
            Pegawai::updateAll([
                'additional_esign_data' => json_encode($additional_esign_data),
            ], [
                'pegawai_id' => $pegawai_id,
            ]);
        } catch (\Exception $e) {
            Yii::error($e);
        }
    }
}