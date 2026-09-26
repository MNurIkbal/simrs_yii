<?php

/**
 ** @author yaya
 ** service :
 ** - Gudang formulir-stok-opname v1
 **/

namespace Doco\gudang\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\gudang\models\InfFormulirSoBarangForm;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use Doco\gudang\components\access\VerifikasiStokOpnameAccess;
use app\components\DHtml;
use yii\helpers\Url;

class InformasiFormulirSoBarangController extends DocoController
{

    protected $_title = "Informasi Formulir Stok Opname Barang";
    protected $_module = '/gudang/informasi-formulir-so-barang';
    protected $_restGudang;
    protected $_restApotek;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $instalasi =  $this->guzzleExec(Yii::$app->docoRest->master, [
            'url' => 'master-api/get-instalasi',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'form_params' => [],
                    'query' => []
                ]
            ]
        ]);

        array_walk($instalasi, function (& $item) {
            $item['id'] = $item['instalasi_id'];
            $item['text'] = $item['instalasi_nama'];
            unset($item['instalasi_id']);
            unset($item['instalasi_nama']);
         });


        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $draw = $request->get('draw', 1);
        $data = [];

        try{

            $body = $this->guzzleExec($this->_restGudang,[
                'url' => 'inf-formulir-so-barang',
                'method' => 'GET',
                'payload' => [
                    'form_params' => [],
                    'query' => $yiiRestfulParams,
                ],
                'returnResponse' => true
            ]);
    
            $no = $request->get('start', 1);
            $data = [];
            foreach ($body['data']['data'] as $key => $value) {
                $no++;
                $value['tglformulir'] = isset($value['tglformulir']) ? date("j M Y H:i:s", strtotime($value['tglformulir'])) : '';
                $value['rowNum'] = $no;
                $value['primary'] = DocoHelpers::encrypt($value['formsobarang_id']);
                $value['tglstokopname'] = isset($value['tglstokopname']) ? date("j M Y H:i:s", strtotime($value['tglstokopname'])) : '';
                $value['ins-ruangan'] = $value['instalasi_nama'].' / '.$value['ruangan_nama'];
                if(empty($value['nostokopname'])){
                    $value['status_so'] = 'Belum Input Hasil';
                }else if(empty($value['pegawaiverifikasi'])){
                    $value['status_so'] = 'Belum Verifikasi';
                }else{
                    $value['status_so'] = 'Sudah Verifikasi';
                }
                $value['stokopnamebarang_id'] = DocoHelpers::encrypt(isset($value['stokopnamebarang_id']) ? $value['stokopnamebarang_id'] : null);
    
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['data']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['data']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        }catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->delete('inf-formulir-so-barang/delete',
                [
                    'form_params' => [],
                    'query' => ['id' => $id],
                ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionView($id)
    {
        $id = DocoHelpers::decrypt($id);
        $title = 'Stok Opname Barang';
        $response = $this->guzzleExec($this->_restGudang, [
            'url' => 'inf-formulir-so-barang/view',
            'payload' => [
                'query' => [
                    'id' => $id,
                ]
            ]
        ]);
        $model = new InfFormulirSoBarangForm;
        $header = isset($response['header']) ? $response['header'] : [];
        $model->attributes = $header;
        $model->total_harga_netto = isset($header['totalharga_sistem']) ? $header['totalharga_sistem'] : 0;
        $model->total_harga_fisik = isset($header['totalharga_fisik']) ? $header['totalharga_fisik'] : 0;
        $model->jenis_stok_opname = isset($header['jenisstokopname']) ? $header['jenisstokopname'] : 0;
        $model->selisih_harga_netto = $model->total_harga_netto - $model->total_harga_fisik;
        $model->is_verifikasi = !empty($header['is_verifikasi']) ? true : false;
        $disabled_revisi = ($model->is_verifikasi || empty($model->stokopnamebarang_id));

        $model->detail = isset($response['detail']) ? $response['detail'] : [];
        $opt_jenis_so = isset($response['opt_jenis_so']) ? $response['opt_jenis_so'] : [];
        $opt_kondisi_barang = isset($response['opt_kondisi_barang']) ? $response['opt_kondisi_barang'] : [];
        $id = DocoHelpers::encrypt($id);
        return $this->render('view', get_defined_vars());
    }

    public function actionSave($id)
    {
        $model = new InfFormulirSoBarangForm;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $post = $request->post('InfFormulirSoBarangForm');
        $model->attributes = $post;
        $model->inputan_so = $request->post('inputan_so');
        $ruanganId = $request->get('ruangan_id', Yii::$app->docoVars->workspace("ruangan_id"));
        if ($model->validate()) {
            return $this->guzzleExec($this->_restGudang, [
                'url' => 'inf-formulir-so-barang/save',
                'method' => 'POST', 
                'payload' => [
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id,
                        'ruangan_id' => $ruanganId
                    ]
                ],
                'returnResponse' => true
            ]);
        } else {
            $response = $model->errors;
        }
        return DocoHelpers::response($response, 422, 'InfFormulirSoBarangForm');
    }

    public function actionBeforePrint($id)
    {
        $title = "Print Stok Opname";
        $id = DocoHelpers::decrypt($id);
        $header = $detail = [];
        try {
            $response = $this->_restGudang->get('inf-formulir-so-barang/before-print',
                [
                    'query' => [
                        'id' => $id,
                    ],
                ]);
            $response = json_decode($response->getBody(), true);
            $header = isset($response['response']['header']) ? $response['response']['header'] : [];
            $detail = isset($response['response']['detail']) ? $response['response']['detail'] : [];
        } catch (RequestException $e) {

        } catch (\Exception $e) {

        }
        $id = DocoHelpers::encrypt($id);
        return $this->renderPartial('cetak', get_defined_vars());
    }

    public function actionExportPdf($id)
    {
        $path = Yii::getAlias("@download") . "/gudang-informasi-so.pdf";
        $response = $this->guzzleExec($this->_restGudang,[
            'url' => 'inf-formulir-so-barang/export-pdf-detail',
            'method' => 'GET',
            'payload' => [
                'query' => ['id' => DocoHelpers::decrypt($id)],
                'save_to' => $path,
            ],
        ]);
        return DocoHelpers::previewPdf($path);
    }

    /**
     * endpoint for datatable detail formulir so barang
     *
     * @param String $noFormulir
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionDatatableDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restGudang->get('inf-formulir-so-barang/datatable-detail',
                [
                    'form_params' => [],
                    'query' => array_merge($yiiRestfulParams, [
                        'noFormulir' => $request->get('noFormulir')
                    ]),
                ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $result['data'] = $body['response']['data'];
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    /**
     * Method to preview pdf detail formulir SO barang
     *
     * @param String $noFormulir
     * @return DOC
     * @author Tsani Nashrullah
     **/
    public function actionPrintDetailFormulir($noFormulir)
    {
        $path = Yii::getAlias("@download") . "/gudang-informasi-detail-formulir-so.pdf";
        try {
            $this->_restGudang->get('inf-formulir-so-barang/export-pdf-detail-formulir', [
                'query' => ['noFormulir' => $noFormulir],
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $this->helper->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = Yii::$app->docoVars->workspace("ruangan_id");

        try{   

            $body = $this->guzzleExec(Yii::$app->docoRest->master,[
                'url' => 'master-api/get-ruangan',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'instalasi_id' => Yii::$app->request->get('additionalPayload', []),
                        'state' => 0,
                    ]
                ],
                'returnResponse' => true
            ]);

            if(!isset($body['data'])){
                return DocoHelpers::response($result,200);    
            }

            foreach ($body['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'text' => $value['ruangan_nama']
                ];

            return $result['output'];
        }
            catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetStokBarang() {
        $request = Yii::$app->request;
        $ruanganId = $request->get('ruangan_id', Yii::$app->docoVars->workspace("ruangan_id"));
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'barang/get-stok',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'barang_id' => $request->get('barang_id', null),
                    'ruangan_id' => $ruanganId
                ]
            ],
            'returnResponse' => true    
        ]);
    }

    public function actionDetailSo($id) 
    {
        $this->_title = Yii::t('fe', 'Detail Stok Opname Barang');
        $title = DHtml::getTitleMenu($this->_title);
        $header = Yii::t('fe', 'Informasi Stok Opname');
        $request = Yii::$app->request;
        $stokopnamebarang_id = $request->get('stokopnamebarang_id', null);

        $response = $this->guzzleExec($this->_restGudang,[
            'url' => 'inf-formulir-so-barang/detail-header',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'id' => DocoHelpers::decrypt($stokopnamebarang_id)
                ],
            ],
        ]);
        $data = ArrayHelper::getValue($response, 'data', []);
        $total_harga_fisik = ArrayHelper::getValue($data, 'harga_netto_fisik', 0);
        $total_harga_sistem = ArrayHelper::getValue($data, 'total_harga_sistem', 0);
        $total_selisih = ArrayHelper::getValue($data, 'total_selisih', 0);
        $is_verifikasi = ArrayHelper::getValue($data, 'is_verifikasi', false);

        $btn_toolbar = [
            'back' => ['attributes' => ['href' => Url::home().("gudang/informasi-formulir-so-barang/index")]],
            'cetak-pdf'=> [
                'title'=> \Yii::t('fe', 'pdf'),
                'icon'=> 'fa fa-file-pdf-o',
                'attributes' => [
                    'data-target' => '/gudang/informasi-formulir-so-barang/export-pdf-detail?id='.$stokopnamebarang_id.'&',
                    'target'=>'_blank',
                    'data-options'=>'pdf'
                ]
            ],
        ];

        $isWithVerified = (new VerifikasiStokOpnameAccess)->checkKonfigOnly();
        if ((new VerifikasiStokOpnameAccess)->check(@$data['is_verifikasi'])) {
            $btn_toolbar['verifikasi'] = [
                'type' => 'button',
                'title' => 'Verifikasi',
                'icon' => 'fa fa-check',
                'attributes' => [
                    'additional' => '',
                    'data-options' => 'click',
                    'id' => 'verifikasi',
                    'data-url' => ''
                ]
            ];
        }

        return $this->render('detail-so', get_defined_vars());
    }

    public function actionGetDataDetailSo($stokopnamebarang_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $restParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $params = array_merge(['stokopnamebarang_id' => DocoHelpers::decrypt($stokopnamebarang_id)], $restParams);
        $response = $this->guzzleExec($this->_restGudang,[
            'url' => 'inf-formulir-so-barang/get-data-detail-so',
            'method' => 'GET',
            'payload' => [
                'query' => $params
            ],
        ]);

        $result = [];
        $data = [];
        $no = $request->get('start',1);
        foreach($response['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['volume_sistem'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'volume_sistem', 0));
            $value['volume_fisik'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'volume_fisik', 0));
            $value['selisih_so'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'selisih_so', 0));
            $value['stok_sistem'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_sistem', 0));
            $value['stok_selisih'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_selisih', 0));
            $value['harganetto'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'harganetto', 0));
            $value['harga_netto_fisik'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'harga_netto_fisik', 0));
            $value['total_selisih'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_selisih', 0));
            
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['draw'] = $request->get('draw', 1);
        $result['recordsTotal'] = ArrayHelper::getValue($response['_meta'], 'totalCount', 0);
        $result['recordsFiltered'] = ArrayHelper::getValue($response['_meta'], 'totalCount', 0);
        return DocoHelpers::response($result);
    }

    public function actionExportPdfDetail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $restParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name');
        $path = Yii::getAlias("@download") . "/detail-so-barang-{$nama_ruangan}-{$id}.pdf";
        
        $params = array_merge(['id' => DocoHelpers::decrypt($id)], $restParams);
        $response = $this->guzzleExec($this->_restGudang,[
            'url' => 'inf-formulir-so-barang/export-pdf-detail',
            'method' => 'GET',
            'payload' => [
                'query' => $params,
                'save_to' => $path,
            ],
        ]);
        return DocoHelpers::previewPdf($path);
    }

    public function actionVerifikasi($id){
        $id = DocoHelpers::decrypt($id);
        $response = $this->guzzleExec($this->_restGudang,[
            'url' => 'inf-formulir-so-barang/verifikasi',
            'method' => 'PUT',
            'payload' => [
                'query' => [
                    'id' => $id
                ],
            ],
            'returnResponse' => true
        ]);
        return $response;
    }

    public function actionShowPopup() {
        $request = Yii::$app->request;
        $title = 'Cetak Formulir Stok Opname Barang';
        $id = $request->get('primary', null);
        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, ['id' => DocoHelpers::decrypt($id)]);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restGudang, [
            'url' => 'inf-formulir-so-barang/sync-pdf',
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
        $this->guzzleExec($this->_restGudang, [
            'url' => 'inf-formulir-so-barang/download-pdf',
            'payload' => [
                'query' => [
                    'fileName' => $fileName,
                ],
                'save_to' => $path
            ]
        ]);
        return DocoHelpers::previewPdf($path);
    }
}
