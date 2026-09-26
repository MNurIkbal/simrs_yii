<?php


namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanStockInventoryBarangController extends DocoController
{
    public $_title = "Laporan Stock Inventory Barang";
    public $_module = '/gudang/laporan-stock-inventory-barang/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanStockInventoryBarang\IndexAction',
            'get-list-data' => 'Doco\gudang\actions\LaporanStockInventoryBarang\GetListDataAction'
        ];
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
    
        $path = Yii::getAlias("@download") . "/laporan-stock-inventory-barang.xlsx";
        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-stock-inventory-barang/export-excel',[
                'save_to' => $path,
                'query' => $filter,
            ],
            'method' => 'get',
            'returnResponse' => true
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Stock Inventory Barang Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-stock-inventory-barang/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-stock-inventory-barang.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = Yii::$app->docoRest->gudang->get('lap-stock-inventory-barang/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
    
    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        if(!empty($parent_label)){
            $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
                'url' => 'allow/get-ruangan',
                'method' => 'get',
                    'payload' => [
                        'query' => [
                            'instalasi_id' => $parent_label,
                        ]
                    ],
                'returnResponse' => true
            ]);
            
            foreach ($response['data']['data'] as $value)
            if($value['instalasi_id'] == $parent_label){
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            }
            return $result;
        }else{
            return $result;
        }
    }
}
 