<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanAdjustment;

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
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $endPoint = $this->controller->_endpoint;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['randString'] = $randString;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => $endPoint . 'sync-export-excel',
            'payload' => [
                'query' => $payload
            ],
        ]);
    }
}
