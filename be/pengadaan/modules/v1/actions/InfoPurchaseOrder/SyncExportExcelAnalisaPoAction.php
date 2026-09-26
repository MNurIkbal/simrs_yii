<?php

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use Doco\Services\InternalService;

class SyncExportExcelAnalisaPoAction extends Action {

    public function run() {
        $request  = Yii::$app->request;
        $getData  = $request->get();
        $xOwner   = $request->getHeaders()->get('X-Owner');
        $auth     = $request->getHeaders()->get('Authorization');
        $fileName = isset($getData['file_name']) ? $getData['file_name'] : 'laporan_analisa_purchase_order';
        $randString = isset($getData['randString']) ? $getData['randString'] : null;

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanAnalisaPoExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanAnalisaPoExcelExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanAnalisaPoExcelUpload' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filename' => $fileName,
                    'ext' => '.xlsx',
                ]
            ]
        ], true);
        return [
            'randString' => $randString
        ];
    }
}
