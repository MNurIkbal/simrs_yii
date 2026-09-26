<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Doco\components\DocoActiveController;

class LapRekapPurchaseOrderObatController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-data'     => 'app\modules\v1\actions\LapRekapPurchaseOrderObat\GetDataAction',
            'export-excel' => 'app\modules\v1\actions\LapRekapPurchaseOrderObat\ExportExcelAction',
            'get-supplier' => 'app\modules\v1\actions\LapRekapPurchaseOrderObat\GetSupplierAction'
        ];
    }
}
