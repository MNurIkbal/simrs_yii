<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class InformasiAdjustmentObatAlkesController extends DocoController {
    protected $allowAction = ['*'];

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'get-data' => 'Doco\gudang\actions\GetDataAction',
            'get-data-adjustment' => 'Doco\gudang\actions\GetDataAdjustmentAction',
            'index' => 'Doco\gudang\actions\IndexAction',
            'search-no-adjustment' => 'Doco\gudang\actions\SearchNoAdjustmentAction',
            'view' => 'Doco\gudang\actions\ViewAction'
        ];
    }

    public function actionCetak($id,$no_adjusmen = null, $jenis_adjusmen = null) {
        $adjustment_id = DocoHelpers::decrypt($id);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
    
        $path = Yii::getAlias("@download") . "/adjustment-obat-alkes.pdf";
        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'adjustment-obat-alkes/export-pdf',
            'payload' => [
                'query' => [
                    'no_adjusmen' => $no_adjusmen,
                    'tipe' => $jenis_adjusmen,
                ],
                'save_to' => $path,
            ]
        ]);
            return DocoHelpers::previewPdf($path);
    }
}