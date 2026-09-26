<?php

namespace Integrasi\Service\Mhg;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\Services\MhgService;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Mhg\Cache\Cache;
use Integrasi\Service\Mhg\Models\PendaftaranOl;

class Appointment extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $payload = $this->result['data'][0];
        $state = $this->state;

        $model = new PendaftaranOl;
        $model->pendaftaranol_id = $payload['pendaftaranol_id'];
        $model->state = $state;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = isset($userIdentity['uid']) ? $userIdentity['uid'] : null;
        $model->payload = isset($payload['additional_data']) ? $payload['additional_data'] : null;
        $model->save();
        
        return json_encode([
            'service' => 'Mhg-Appointment',
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $this->attributes,
            'result' => $model
        ]);
    }

    public function saveLogs($data)
    {
        return PendaftaranOl::batchInsert($data);
    }
}