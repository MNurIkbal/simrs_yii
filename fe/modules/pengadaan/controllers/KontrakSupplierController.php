<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use app\components\DocoController;

class KontrakSupplierController extends DocoController
{
	protected $allowAction = ['*'];
    public $_title = "Kontrak Supplier";
    public $_module = '/pengadaan/kontrak-supplier/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'create'            => 'Doco\pengadaan\actions\KontrakSupplier\CreateAction',
            'index'             => 'Doco\pengadaan\actions\KontrakSupplier\IndexAction',
            'get-list-data'     => 'Doco\pengadaan\actions\KontrakSupplier\GetListDataAction',
            'save'              => 'Doco\pengadaan\actions\KontrakSupplier\SaveAction',
            'search-obat'       => 'Doco\pengadaan\actions\KontrakSupplier\SearchObatAction',
            'search-supplier'   => 'Doco\pengadaan\actions\KontrakSupplier\SearchSupplierAction',
            'edit'              => 'Doco\pengadaan\actions\KontrakSupplier\EditAction',
            'update'            => 'Doco\pengadaan\actions\KontrakSupplier\UpdateAction',
            'detail'            => 'Doco\pengadaan\actions\KontrakSupplier\DetailAction',
            'change-status'     => 'Doco\pengadaan\actions\KontrakSupplier\ChangeStatusAction',
            'import-data' => 'Doco\pengadaan\actions\KontrakSupplier\ImportDataAction',
            'export-download-excel' => 'Doco\pengadaan\actions\KontrakSupplier\ExportDownloadExcelAction',
            'upload' => 'Doco\pengadaan\actions\KontrakSupplier\UploadProcessAction',
            'upload-kontrak-supplier' => 'Doco\pengadaan\actions\KontrakSupplier\UploadKontrakSupplierAction',
        ];
    }
}
