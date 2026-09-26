<?php

namespace Doco\pengadaan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;

use app\modules\pengadaan\models\InfoPoForm;

define("DOWNLOAD", "@download");

class InfoPurchaseOrderController extends DocoController
{
    protected $_title;
    protected $_restPengadaan;
    protected $_restMaster;
    protected $_module = '/pengadaan/info-purchase-order/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Purchase Order (PO)');
        $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions() {
        $actions = parent::actions();
        $action = [
            'index'                     => 'Doco\pengadaan\actions\InfoPurchaseOrder\IndexAction',
            'detail'                    => 'Doco\pengadaan\actions\InfoPurchaseOrder\DetailAction',
            'get-data'                  => 'Doco\pengadaan\actions\InfoPurchaseOrder\GetDataAction',
            'get-satuan-konversi-item'  => 'Doco\pengadaan\actions\InfoPurchaseOrder\GetSatuanKonversiItemAction',
            'save'                      => 'Doco\pengadaan\actions\InfoPurchaseOrder\SaveAction',
            'search-item'               => 'Doco\pengadaan\actions\InfoPurchaseOrder\SearchItemAction',
            'edit'                      => 'Doco\pengadaan\actions\InfoPurchaseOrder\EditAction',
            'log-edit'                  => 'Doco\pengadaan\actions\InfoPurchaseOrder\LogEditAction',
            'get-log-activity'          => 'Doco\pengadaan\actions\InfoPurchaseOrder\GetLogActivityAction',
            'split-po'                  => 'Doco\pengadaan\actions\InfoPurchaseOrder\SplitPOAction',
            'split-po-save'             => 'Doco\pengadaan\actions\InfoPurchaseOrder\SplitPOSaveAction',
            'merge-po'                  => 'Doco\pengadaan\actions\InfoPurchaseOrder\MergePoAction',
        ];
        return array_merge($actions,$action);
    }

    public function actionDelete($id, $type_po, $catatan)
    {
        try {
            $response = $this->_restPengadaan->delete('info-purchase-order/delete', [
                'query' => [
                    'id' => DocoHelpers::decrypt($id),
                    'type_po' => DocoHelpers::decrypt($type_po)
                ],
                'form_params' => [
                    'catatan' => $catatan
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response,false);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }
    }

    protected function processPrintDetail($is_kop = false)
    {
        $request = Yii::$app->request;
        if(!$request->get('no_transaksi') || !$request->get('type_po')){
            throw new \yii\web\NotFoundHttpException();
        }

        $url = $is_kop ? 'info-purchase-order/cetak-rincian-kop' : 'info-purchase-order/cetak-rincian';
        $statusKop = $is_kop ? 1 : 0;

        if(Yii::$app->report->enabled){
            $report_code = 'pengadaan/'.$url;
            $query_params = [
                'nomor_po' => $request->get('no_transaksi'),
                'type_po' => DocoHelpers::decrypt($request->get('type_po')),
                'statusKop' => $statusKop,
                'userid' => Yii::$app->user->identity->loginpemakai_id
            ];
            return Yii::$app->report->exec($report_code.'?'.http_build_query($query_params),[
                'manualRender' => function() use ($request,$url){
                    $no_transaksi = is_array($request->get('no_transaksi')) ? $request->get('no_transaksi') : [$request->get('no_transaksi')];
                    $type_po = is_array($request->get('type_po')) ? $request->get('type_po') : [$request->get('type_po')];
                    $path = Yii::getAlias(DOWNLOAD) . "/rincian-info-purchase-order.pdf";
                    $this->guzzleExec($this->_restPengadaan, [
                        'url' => $url,
                        'payload' => [
                            'query' => [
                                'no_transaksi' => array_map(function ($val) {
                                    return $val;
                                }, $no_transaksi),
                                'type_po' => array_map(function ($val) {
                                    return DocoHelpers::decrypt($val);
                                }, $type_po),
                            ],
                            'save_to' => $path
                        ]
                    ]);
                    return DocoHelpers::previewPdf($path);
                }
            ]);
        }

        $no_transaksi = is_array($request->get('no_transaksi')) ? $request->get('no_transaksi') : [$request->get('no_transaksi')];
        $type_po = is_array($request->get('type_po')) ? $request->get('type_po') : [$request->get('type_po')];
        $path = Yii::getAlias(DOWNLOAD) . "/rincian-info-purchase-order.pdf";
        $this->guzzleExec($this->_restPengadaan, [
            'url' => $url,
            'payload' => [
                'query' => [
                    'no_transaksi' => array_map(function ($val) {
                        return $val;
                    }, $no_transaksi),
                    'type_po' => array_map(function ($val) {
                        return DocoHelpers::decrypt($val);
                    }, $type_po),
                ],
                'save_to' => $path
            ]
        ]);
        return DocoHelpers::previewPdf($path);
    }

    public function actionCetakRincian()
    {
        $this->processPrintDetail(false);

        /*
        $request = Yii::$app->request;
        $no_transaksi = is_array($request->get('no_transaksi')) ? $request->get('no_transaksi') : [$request->get('no_transaksi')];
        $type_po = is_array($request->get('type_po')) ? $request->get('type_po') : [$request->get('type_po')];
        $path = Yii::getAlias(DOWNLOAD) . "/rincian-info-purchase-order.pdf";
        $this->guzzleExec($this->_restPengadaan, [
            'url' => 'info-purchase-order/cetak-rincian',
            'payload' => [
                'query' => [
                    'no_transaksi' => $no_transaksi,
                    'type_po' => array_map(function ($val) {
                        return DocoHelpers::decrypt($val);
                    }, $type_po),
                ],
                'save_to' => $path
            ]
        ]);
        return DocoHelpers::previewPdf($path);
        */
    }

    public function actionCetakRincianKop() // parameter send, $id & $po
    {
        $this->processPrintDetail(true);

        /*
        $request = Yii::$app->request;
        if(!$request->get('no_transaksi') || !$request->get('type_po')){
            throw new \yii\web\NotFoundHttpException(); // return not found jika tidak ada id dan type_po
        }
        $no_transaksi = is_array($request->get('no_transaksi')) ? $request->get('no_transaksi') : [$request->get('no_transaksi')];
        $type_po = is_array($request->get('type_po')) ? $request->get('type_po') : [$request->get('type_po')];
        $path = Yii::getAlias(DOWNLOAD) . "/rincian-info-purchase-order.pdf";
        $this->guzzleExec($this->_restPengadaan, [
            'url' => 'info-purchase-order/cetak-rincian-kop',
            'payload' => [
                'query' => [
                    'no_transaksi' => array_map(function ($val) {
                        return $val;
                    }, $no_transaksi),
                    'type_po' => array_map(function ($val) {
                        return DocoHelpers::decrypt($val);
                    }, $type_po),
                ],
                'save_to' => $path
            ]
        ]);
        return DocoHelpers::previewPdf($path);
        */
    }

    public function actionValidasi($id, $type_po)
    {
        $request = Yii::$app->request;
        $model = new InfoPoForm;
        $model->load($request->post());
        $model->list_data = $request->post('list_data','{}');
        try {
            if ($model->validate()) {
                $response = $this->_restPengadaan->post('info-purchase-order/save', [
                    'query' => [
                        'id' => DocoHelpers::decrypt($id),
                        'type_po' => DocoHelpers::decrypt($type_po)
                    ],
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(),true);
            } else {
                return DocoHelpers::response($model->errors, 422, 'InfoPoForm');
            }

        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($filter['advanced-filter']['tanggal_po'])) {
            $tgl_po = explode(' - ', $filter['advanced-filter']['tanggal_po']);
            $tgl_awal_validasi = $tgl_po[0];
            $tgl_akhir_validasi = $tgl_po[1];
            $tgl_awal_format_validasi = date('Y-m-d H:i:s', strtotime($tgl_awal_validasi . ' 00:00:00'));
            $tgl_akhir_format_validasi = date('Y-m-d H:i:s', strtotime($tgl_akhir_validasi . ' 23:59:59'));
            $filter['advanced-filter']['tanggal_po_awal_validasi'] = $tgl_awal_format_validasi;
            $filter['advanced-filter']['tanggal_po_akhir_validasi'] = $tgl_akhir_format_validasi;

            unset($filter['advanced-filter']['tanggal_po']);
        }

        if (isset($filter['advanced-filter']['tanggal_buat_po'])) {
            $tgl_buat_po = explode(' - ', $filter['advanced-filter']['tanggal_buat_po']);
            $tgl_awal = $tgl_buat_po[0];
            $tgl_akhir = $tgl_buat_po[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tanggal_po_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tanggal_po_akhir'] = $tgl_akhir_format;

            unset($filter['advanced-filter']['tanggal_buat_po']);
        }

        if (isset($request->get()['po_cito'])) {
            $filter['advanced-filter']['po_cito'] = $request->get('po_cito');
        }

        if (isset($request->get()['po_admin'])) {
            $filter['advanced-filter']['po_admin'] = $request->get('po_admin');
        }

        if (isset($request->get()['po_consigment'])) {
            $filter['advanced-filter']['po_consigment'] = $request->get('po_consigment');
        }

        $filter['ruangan_id'] = $ruangan_id;

        $path = Yii::getAlias(DOWNLOAD) . "/info-purchase-order.pdf";

        $this->guzzleExec($this->_restPengadaan, [
            'url' => 'info-purchase-order/cetak-pdf',
            'payload' => [
                'query' => $filter,
                'save_to' => $path
            ]
        ]);

        return DocoHelpers::previewPdf($path);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($filter['advanced-filter']['tanggal_po'])) {
            $tgl_po = explode(' - ', $filter['advanced-filter']['tanggal_po']);
            $tgl_awal_validasi = $tgl_po[0];
            $tgl_akhir_validasi = $tgl_po[1];
            $tgl_awal_format_validasi = date('Y-m-d H:i:s', strtotime($tgl_awal_validasi . ' 00:00:00'));
            $tgl_akhir_format_validasi = date('Y-m-d H:i:s', strtotime($tgl_akhir_validasi . ' 23:59:59'));
            $filter['advanced-filter']['tanggal_po_awal_validasi'] = $tgl_awal_format_validasi;
            $filter['advanced-filter']['tanggal_po_akhir_validasi'] = $tgl_akhir_format_validasi;

            unset($filter['advanced-filter']['tanggal_po']);
        }

        if (isset($filter['advanced-filter']['tanggal_buat_po'])) {
            $tgl_buat_po = explode(' - ', $filter['advanced-filter']['tanggal_buat_po']);
            $tgl_awal = $tgl_buat_po[0];
            $tgl_akhir = $tgl_buat_po[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tanggal_po_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tanggal_po_akhir'] = $tgl_akhir_format;

            unset($filter['advanced-filter']['tanggal_buat_po']);
        }

        if (isset($request->get()['po_cito'])) {
            $filter['advanced-filter']['po_cito'] = $request->get('po_cito');
        }
        if (isset($request->get()['po_admin'])) {
            $filter['advanced-filter']['po_admin'] = $request->get('po_admin');
        }
        if (isset($request->get()['po_consigment'])) {
            $filter['advanced-filter']['po_consigment'] = $request->get('po_consigment');
        }
        
        $path = Yii::getAlias(DOWNLOAD) . "/info-purchase-order.xlsx";
        $this->guzzleExec($this->_restPengadaan, [
            'url' => 'info-purchase-order/export-excel',
            'payload' => [
                'query' => $filter,
                'save_to' => $path
            ]
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionGetSupplier()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restPengadaan->get('allow/list-supplier',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $value) {
                $response[] = [
                    'id' => $value['supplier_id'],
                    'text' => $value['supplier_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionGetSupplierWithPrice()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restPengadaan->get('info-purchase-order/get-supplier-with-price',[
                            'query' => [
                                'term' => $request->get('term'),
                                'obatalkes_id' => $request->get('obatalkes_id')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $value) {
                $response[] = [
                    'id' => $value['supplier_id'],
                    'text' => $value['supplier_nama'],
                    'harga' => $value['supplier_harga']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionGetRuangan()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restPengadaan->get('allow/get-ruangan',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $value) {
                $response[] = [
                    'id' => $value['ruangan_id'],
                    'text' => $value['ruangan_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionSearchPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-pegawai',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionShowPopupRincian() {
        $request = Yii::$app->request;
        $no_transaksi = $request->get('no_transaksi', []);
        $type_po = $request->get('type_po', []);
        $is_kop = $request->get('is_kop', false);
        $titleKop = $is_kop ? 'Kop' : '';
        $title = 'Cetak Rincian '. $titleKop ;
        $params = [
            'no_transaksi' => array_map(function ($val) {
                return $val;
            }, $no_transaksi),
            'type_po' => array_map(function ($val) {
                return DocoHelpers::decrypt($val);
            }, $type_po),
            'is_kop' => $is_kop,
            'is_bgprocess' => true
        ];
        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $params);
        return $this->renderAjax('_modalCetakRincian', get_defined_vars());
    }

    public function actionProcessSyncRincian($randString) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $is_kop = ArrayHelper::getValue($session, 'is_kop', false);
        return $this->guzzleExec($this->_restPengadaan, [
            'url' => 'info-purchase-order/process-sync-rincian',
            'payload' => [
                'query' => [
                    'params' => $session,
                    'randString' => $randString,
                ]
            ]
        ]);
    }

    public function actionDownloadZip() 
    {
        $request = Yii::$app->request;
        $fileDownloads = $request->get('fileName', null);
        $isKop = $request->get('isKop', false);
        $str = !empty($isKop) ? ' Kop' : '';
        $fileName = "Cetak Rincian{$str}.zip";
        $path = Yii::getAlias("@download") . '/' . $fileName;
        $this->guzzleExec($this->_restPengadaan, [
            'url' => 'info-purchase-order/download-zip',
            'payload' => [
                'query' => [
                    'fileName' => $fileDownloads,
                ],
                'save_to' => $path
            ]
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
