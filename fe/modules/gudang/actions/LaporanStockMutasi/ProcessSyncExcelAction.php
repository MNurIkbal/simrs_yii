<?php

namespace Doco\gudang\actions\LaporanStockMutasi;

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
        
        return $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-stock-mutasi/sync-export-excel',
            'payload' => [
                'query' => $payload
            ],
        ]);
    }
}
