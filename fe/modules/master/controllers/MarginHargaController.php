<?php

/**
 * @Author: Budi
 * @Date:   2019-02-25 16:15:00
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;

class MarginHargaController extends DocoController {
    public function init() {
        parent::init();
    }

    public function behaviors() {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actions() {
        return [
            'change-status'                 => 'Doco\master\actions\MarginHarga\ChangeStatusAction',
            'check-transaction'             => 'Doco\master\actions\MarginHarga\CheckTransactionAction',
            'create'                        => 'Doco\master\actions\MarginHarga\CreateAction',
            'data-detail'                   => 'Doco\master\actions\MarginHarga\DataDetailAction',
            'delete'                        => 'Doco\master\actions\MarginHarga\DeleteAction',
            'delete-cache'                  => 'Doco\master\actions\MarginHarga\DeleteCacheAction',
            'detail-margin-harga-obat'      => 'Doco\master\actions\MarginHarga\DetailMarginHargaObatAction',
            'export-excel'                  => 'Doco\master\actions\MarginHarga\ExportExcelAction',
            'get-data'                      => 'Doco\master\actions\MarginHarga\GetDataAction',
            'get-list-item'                 => 'Doco\master\actions\MarginHarga\GetListItemAction',
            'group-margin'                  => 'Doco\master\actions\MarginHarga\GroupMarginAction',
            'group-margin-create'           => 'Doco\master\actions\MarginHarga\GroupMarginCreateAction',
            'group-margin-get-data'         => 'Doco\master\actions\MarginHarga\GroupMarginGetDataAction',
            'index'                         => 'Doco\master\actions\MarginHarga\IndexAction',
            'list-data-margin'              => 'Doco\master\actions\MarginHarga\ListDataMarginAction',
            'margin-harga-obat'             => 'Doco\master\actions\MarginHarga\MarginHargaObatAction',
            'margin-harga-obat-get-data'    => 'Doco\master\actions\MarginHarga\MarginHargaObatGetDataAction',
            'margin-khusus'                 => 'Doco\master\actions\MarginHarga\MarginKhususAction',
            'margin-khusus-detail'          => 'Doco\master\actions\MarginHarga\MarginKhususDetailAction',
            'margin-khusus-detail-get-data' => 'Doco\master\actions\MarginHarga\MarginKhususDetailGetDataAction',
            'margin-khusus-get-data'        => 'Doco\master\actions\MarginHarga\MarginKhususGetDataAction',
            'margin-khusus-tambah'          => 'Doco\master\actions\MarginHarga\MarginKhususTambahAction',
            'set-list-item'                 => 'Doco\master\actions\MarginHarga\SetListItemAction',
            'update'                        => 'Doco\master\actions\MarginHarga\UpdateAction'
        ];
    }
}
