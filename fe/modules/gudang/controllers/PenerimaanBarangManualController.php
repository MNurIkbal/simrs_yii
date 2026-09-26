<?php

namespace Doco\gudang\controllers;

use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\gudang\models\PenerimaanSupplierDetailForm;
use app\modules\gudang\models\PenerimaanSupplierForm;
use Yii;
use yii\web\Response;

class PenerimaanBarangManualController extends DocoController
{
    protected $title = "Penerimaan Barang Manual";
    protected $_restMaster;
    protected $_restGudang;
    protected $helper;

    /**
     * Init controller / Constructor
     **/
    public function init()
    {
        parent::init();
        $this->helper = new DocoHelpers;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    /**
     * Return view index transaction penerimaan barang manual
     *
     * @return view
     * @author Tsani Nashrullah
     **/
    public function actionIndex()
    {
        $title = $this->title;
        $model = new PenerimaanSupplierForm;
        $model->scenario = PenerimaanSupplierForm::SCENARIO_GUDANG_UMUM;
        $modelDetail = new PenerimaanSupplierDetailForm;
        $options = $this->helper->guzzleExec($this->_restGudang, [
            'url' => 'penerimaan-barang-manual/get-attribute',
            'method' => 'get',
        ]);
        $konfig_farmasi = $this->_restGudang->get('konfig-farmasi/get-konfig?id=1');
        $setting = json_decode($konfig_farmasi->getBody(),true);
        $harga_donasi = $setting['response']['harga_donasi'];
        return $this->render('index', get_defined_vars());
    }

    /**
     * Source Data dropdown
     *
     * @param String $type
     * @param Array $payload
     * @return JSON
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function actionSourceData()
    {
        $payload = Yii::$app->request->get();
        if (isset($payload['type'])) {
            $payloadToApi = isset($payload['payload']) ? $payload['payload'] : [];
            $result = [];
            switch ($payload['type']) {
                case 'supplier':
                    $optionApi = [
                        'url' => 'allow/list-supplier',
                        'method' => 'get',
                    ];
                    break;
                case 'pegawai':
                    $optionApi = [
                        'url' => 'allow/list-pegawai',
                        'method' => 'get',
                    ];
                    break;
                case 'barang':
                    $optionApi = [
                        'url' => 'allow/list-barang',
                        'method' => 'get',
                    ];
                    break;
                case 'satuanKonversiBarang':
                    $optionApi = [
                        'url' => 'allow/list-satuan-konversi-barang-adjustment',
                        'method' => 'get',
                    ];
                    $payloadToApi = [
                        'groupByBarang' => $payload['barang_id'],
                    ];
                    break;
                default:
                    return $this->helper->macroResponseJson(400, 'Tipe tidak ditemukan');
                    break;
            }

            $result = $this->helper->guzzleExec($this->_restMaster, $optionApi, [
                'query' => $payloadToApi,
            ]);
            return $this->helper->macroResponseJson(200, 'Berhasil mendapatkan data', $result);
        } else {
            return $this->helper->macroResponseJson(400, 'Tipe tidak ditemukan');
        }
    }

    /**
     * Store data to backend
     *
     * @param Array $payload
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionStore()
    {
        $payload = Yii::$app->request->post();

        $form = new PenerimaanSupplierForm;
        $form->scenario = PenerimaanSupplierForm::SCENARIO_GUDANG_UMUM;
        $form->attributes = $payload['PenerimaanSupplierForm'];
        $payload['PenerimaanSupplierForm']['is_donasi'] = $payload['isDonasiHeader'];
        if ($form->validate()) {
            $responseBackend = $this->helper->guzzleExec($this->_restGudang, [
                'url' => 'penerimaan-barang-manual/store',
                'method' => 'post',
            ], [
                'form_params' => [
                    'PenerimaanSupplier' => $payload['PenerimaanSupplierForm'],
                    'PenerimaanSupplierDetail' => $payload['PenerimaanSupplierDetailForm'],
                ],
            ], true);
            if (isset($responseBackend['httpStatusCode']) && $responseBackend['httpStatusCode'] >= 200 && $responseBackend['httpStatusCode'] <= 299) {
                return $this->helper->macroResponseJson(200, 'Transaksi penerimaan barang manual berhasil disimpan.', $responseBackend);
            } else {
                return $this->helper->macroResponseJson($responseBackend['httpStatusCode'], $responseBackend['message']);
            }
        } else {
            return $this->helper->response($form->errors, 422, 'PenerimaanSupplierForm');
        }
    }

    /**
     * Print data penerimaan barang by no pemesanan
     *
     * @param String $noPenerimaan
     * @return Method
     * @author Tsani Nashrullah
     **/
    public function actionCetak($noPenerimaan)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/penerimaan-barang-manual.pdf";

        $response = $this->_restGudang->get('penerimaan-barang-manual/export-pdf?noPenerimaan='.$noPenerimaan,
        [
            'save_to' => $path,
        ]);
        return DocoHelpers::previewPdf($path);
    }
}
