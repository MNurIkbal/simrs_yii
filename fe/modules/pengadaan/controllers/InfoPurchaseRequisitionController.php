<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;

class InfoPurchaseRequisitionController extends DocoController {
    protected $allowAction = ['*'];
    public $_title = "Informasi Purchase Requisition";
    public $_module = '/pengadaan/info-purchase-requisition/';
    protected $_restPengadaan;

    public function init() {
        parent::init();
        $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
    }

    public function actions() {
        $path = 'Doco\pengadaan\actions\InfoPurchaseRequisition';

        return [
            'index'                 => $path . '\IndexAction',
            'detail'                => $path . '\DetailAction',
            'expand'                => $path . '\ExpandAction',
            'list-detail'           => $path . '\ListDetailAction',
            'get-list-data'         => $path . '\GetListDataAction',
            'generate-po'           => $path . '\GeneratePOAction',
            'cetak-detail-pdf'      => $path . '\CetakDetailPdfAction',
            'barang'                => $path . '\BarangAction',
            'get-list-data-obat'    => $path . '\GetListDataObatAction',
            'get-list-data-barang'  => $path . '\GetListDataBarangAction',
            'approving-process'     => $path . '\ApprovingProcessAction'
        ];
    }

    public function actionShowPopup() {
        $request = Yii::$app->request;
        $title = 'Cetak Detail PR';
        $id = $request->get('id', null);
        $params = [
            'id' => DocoHelpers::decrypt($id),
            'type' => $request->get('type', null),
            'order' => $request->get('order', null)
        ];
        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $params);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restPengadaan, [
            'url' => "purchase-requisition/sync-pdf",
            'payload' => [
                'query' => [
                    'params' => $session,
                    'randString' => $randString,
                ]
            ]
        ]);
    }

    public function actionDownloadPdf() {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $path = Yii::getAlias("@download") . '/' . $fileName;
        $response = $this->_restPengadaan->get('purchase-requisition/download-pdf', [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
}
