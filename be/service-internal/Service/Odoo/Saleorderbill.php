<?php

namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Components\Services\OdooService;

class Saleorderbill extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $sendOdoo = (new OdooService)->hitOdoo('/saleorderbill/hit', [], function ($data) {
            return $data;
        });

        return json_encode([
            'service' => 'Odoo-SaleorderBill',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $sendOdoo
        ]);
    }

}