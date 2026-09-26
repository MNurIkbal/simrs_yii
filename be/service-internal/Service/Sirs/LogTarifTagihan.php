<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\LogPelayanan;

class LogTarifTagihan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $attributes = $this->log;

        LogPelayanan::batchInsert($attributes);

        return json_encode([
            'service' => 'Sirs-LogTarifTagihan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

}