<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Components\Services\OdooService;

class Saleorderlineobat extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $sendOdoo = (new OdooService)->hitOdoo('/saleorderline/obat', [], function ($data) {
            return $data;
        });

        return json_encode([
            'service' => 'Odoo-SaleorderlineObat',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $sendOdoo
        ]);
    }

}