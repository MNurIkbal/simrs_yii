<?php

namespace Doco\gudang\actions\LaporanPemakaianBarang;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\Traits\ControllerHelperTrait;

class ProcessSyncExcelAction extends Action
{
    use ControllerHelperTrait;
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['randString'] = $randString;

        if(isset($payload['advanced-filter']['tgl_transaksi'])) {
            $explode = explode(' - ', $payload['advanced-filter']['tgl_transaksi']);
            $payload['advanced-filter']['tgl_transaksi'] = date('Y-m-d', strtotime($explode[0])).' - '.date('Y-m-d', strtotime($explode[1]));
        }else{
            $payload['advanced-filter']['tgl_transaksi'] = date('Y-m-d').' - '.date('Y-m-d');
        }
        
        return $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-pemakaian-barang/sync-export-excel',
            'payload' => [
                'query' => $payload
            ],
        ]);
    }
}
