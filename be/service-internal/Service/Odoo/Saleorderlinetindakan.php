<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Components\Services\OdooService;

class Saleorderlinetindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $sendOdoo = (new OdooService)->hitOdoo('/saleorderline/hit', [], function ($data) {
            return $data;
        });

        return json_encode([
            'service' => 'Odoo-SaleorderlineTindakan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $sendOdoo
        ]);
    }

}