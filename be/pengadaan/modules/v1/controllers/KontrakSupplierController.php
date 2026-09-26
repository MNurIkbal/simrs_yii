<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Doco\components\DocoActiveController;

class KontrakSupplierController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update-status"] = ["PUT"];
        return $verbs;
    }

    public function actions()
    {
        return [
            'edit-filler' => 'app\modules\v1\actions\KontrakSupplier\EditFillerAction',
            'get-no-kontrak' => 'app\modules\v1\actions\KontrakSupplier\GetNoKontrakAction',
            'get-master-obat' => 'app\modules\v1\actions\Allow\GetMasterObatAction',
            'get-list-data' => 'app\modules\v1\actions\KontrakSupplier\GetListDataAction',
            'save' => 'app\modules\v1\actions\KontrakSupplier\SaveAction',
            'update-kontrak-supplier' => 'app\modules\v1\actions\KontrakSupplier\UpdateAction',
            'update-status' => 'app\modules\v1\actions\KontrakSupplier\UpdateStatusAction',
            'export-download-excel' => 'app\modules\v1\actions\KontrakSupplier\ExportDownloadExcelAction',
            'mapping-validasi-data-excel' => 'app\modules\v1\actions\KontrakSupplier\MappingValidasiDataExcelAction',
            'upload-kontrak-supplier' => 'app\modules\v1\actions\KontrakSupplier\UploadKontrakSupplierAction',
        ];
    }
}
