<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-12-06 11:23:07
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 18:21:21
 */
namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\components\access\VerifikasiStokOpnameAccess as VerifikasiStokOpname;
use Doco\apotek\components\access\FormulirStokOpnameAccess as FormulirStokOpname;
use Doco\apotek\components\access\StokOpnameAccess as StokOpname;


class InformasiStokOpnameController extends DocoController
{
    protected $_title = "Informasi Stok Opname";
    protected $_module = 'apotek/informasi-stok-opname/';
    protected $_restApotek; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek; 
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace('ruangan_id');
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-stok-opname/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['stokopname_id']);
                unset($value['stokopname_id']);
                $value['status_verifikasi'] = $value['is_verifikasi'] ? 'Sudah Verifikasi' : 'Belum Verifikasi';
                $value['tglstokopname'] = date("j M Y H:i:s", strtotime($value['tglstokopname']));
                $value['selisih'] = DocoHelpers::rupiahDisplay(abs($value['totalharga_fisik'] - $value['totalharga_sistem']));
                $value['totalharga_fisik'] = DocoHelpers::rupiahDisplay($value['totalharga_fisik']);
                $value['totalharga_sistem'] = DocoHelpers::rupiahDisplay($value['totalharga_sistem']);
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function getListRak() {
        try {
            $request = Yii::$app->request;
            $get = $this->_restApotek->get('allow/get-rakobat');
            $body = json_decode($get->getbody(), true);
            $response = $body['response'];

            return $response;
        } catch(RequestException $e){
            return [];
        } catch(\Exception $e){
            return [];
        }
    }

    public function actionGetDataDetail($id)
    {
        $parent_id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $konfigFarmasi = $this->getKonfig();
        $configBasePriceVal = $konfigFarmasi['base_price_so'];
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $list_data = $this->getListRak();
            $list_rak = ArrayHelper::map($list_data, 'rakobat_id', 'rakobat_nama');
            
            $response = $this->_restApotek->get('inf-stok-opname/detail?parent_id='.($parent_id).'&'.http_build_query($yiiRestfulParams).'&start='.$request->get('start').'&length='.$request->get('length').'&pagination=0');
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['rakobat_nama'] = !is_null($value['rakobat_nama']) ? $value['rakobat_nama'] : 'Tanpa Rak';
                $value['laci'] = !is_null($value['laci']) ? $value['laci'] : 'Tanpa Rak';
                $value['selisih'] = $value['volume_fisik'] - $value['volume_sistem'];
                $value['volume_sistem'] = DocoHelpers::formatNumber($value['volume_sistem'], true, false, 3);
                $value['volume_fisik'] = DocoHelpers::formatNumber($value['volume_fisik'], true, false, 3);
                $value['selisih'] = DocoHelpers::formatNumber($value['selisih'], true, false, 3);
                $value['stok_sistem'] = DocoHelpers::formatNumber($value['stok_sistem'], true, false, 3);
                $value['stok_selisih'] = DocoHelpers::formatNumber($value['stok_selisih'], true, false, 3);

                switch ($configBasePriceVal) {
                    case '1':
                        $value['weighted_avg'] = DocoHelpers::formatNumber($value['base_price'], true, false, 3);
                        $value['selisih_weighted_avg'] = DocoHelpers::formatNumber($value['selisih_base_price'], true, false, 3);
                        break;

                    default:
                        $value['weighted_avg'] = DocoHelpers::formatNumber($value['weighted_avg'], true, false, 3);
                        $value['selisih_weighted_avg'] = DocoHelpers::formatNumber($value['selisih_weighted_avg'], true, false, 3);
                        break;
                }

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetail($id)
    {
        $title = Yii::t('fe', 'Detail Stok Opname');
        $header = $this->_title;

        $request = Yii::$app->request;
        $stokopname_id = $request->get('stokopname_id', null);
        $parent_id = $stokopname_id;
        $decId = DocoHelpers::decrypt($stokopname_id);
        $konfigFarmasi = $this->getKonfig();
        $configBasePriceVal = $konfigFarmasi['base_price_so'];
        $data = [];
        
        try {
            $response = $this->_restApotek->get(
                'inf-stok-opname/get-header-detail', 
                [
                    'query'=>[
                        'id' => $decId,
                        'type' => $configBasePriceVal
                    ]
                ]
            );
            $body =json_decode($response->getBody(), true);
            $data = $body['response'];
        } catch (\RequestException $e) {
            $data = [];
        }

        $isWithVerified = (new VerifikasiStokOpname)->checkKonfigOnly();

        $btn_toolbar = [
                        'back' => ['attributes' => ['href' => Url::home().("apotek/informasi-stok-opname/inf-stok-formulir-opname")]],
                        'cetak-pdf'=> [
                            'title'=> \Yii::t('fe', 'pdf'),
                            'icon'=> 'fa fa-file-pdf-o',
                            'attributes' => [
                                'data-target' => '/apotek/informasi-stok-opname/export-pdf?id='.$parent_id,
                                'target'=>'_blank',
                                'data-options'=>'link'
                            ]
                        ],
                    ];

        if((new VerifikasiStokOpname)->check(@$data['is_verifikasi'])){
            $btn_toolbar['verifikasi'] = [
                            'type' => 'button',
                            'title' => 'Verifikasi',
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'additional' => '',
                                'data-options' => 'click',
                                'id' => 'verifikasi',
                                'data-url' => '/apotek/informasi-stok-opname/verifikasi?id='.$parent_id
                            ]
                        ];
        }

        return $this->render('detail', get_defined_vars());
    }

    public function actionVerifikasi($id)
    {
        $id = DocoHelpers::decrypt($id);
        return (new VerifikasiStokOpname)->verify($id);
    }

    public function actionExportPdf($id)
    {
        $request = Yii::$app->request;
        $konfigFarmasi = $this->getKonfig();
        $configBasePriceVal = $konfigFarmasi['base_price_so'];

        $id_decrypt = DocoHelpers::decrypt($id);
        $nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name');

        $path = Yii::getAlias("@download") . "/detail-so-{$nama_ruangan}-{$id}.pdf";
        $query = 'id=' . $id_decrypt . '&type=' . $configBasePriceVal;
        try {
            $response = $this->_restApotek->get('inf-stok-opname/export-pdf?'. $query, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionInfStokFormulirOpname()
    {
        $title = "Informasi Formulir Dan Stok Opname";
        $default_url = Url::home().(Yii::$app->controller->module->id."/".Yii::$app->controller->id);
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi = [];
        try {
            $response = $this->_restApotek->get('allow/get-instalasi/',
                [
                    'form_params' => [],
                    'query' => []
                ]);
            $response = json_decode($response->getBody(),true);
            $instalasi = isset($response['response']['instalasi']) ? $response['response']['instalasi'] : [];

        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }

        $btn_toolbar = [
            'search'=> [
                'attributes'=>[
                    'id' => 'find-data',
                ]
            ],
            'reset'=> [
                'attributes'=>[
                    'id' => 'reset-data',
                    'data-parent' => '.filter-form'
                ]
            ],
            'export-pdf-serconn' => [
                'type' => 'button',
                'icon' => 'fa fa-file-pdf-o',
                'title' => \Yii::t('fe', 'Cetak Formulir SO'),
                'attributes' => [
                    'id' => 'cetak-rincian-tagihan',
                    'data-options' => 'excel-serconn',
                    'data-target' => '#modal_backdrop',
                    'data-width' => '75%',
                    'data-table-id' => 'example',
                    'data-url' => Url::home() . ('apotek/informasi-stok-opname/show-popup?'),
                    'data-conditions' => 'primary,noformulir,ruangan_nama',
                    'disabled' => 'true',
                ]
            ],
        ];

        if((new FormulirStokOpname)->check()){
            $btn_toolbar['custom-stokopname'] = [
                'type' => 'button',
                'title' => Yii::t('fe', 'Stok opname'),
                'icon' => 'fa fa-shopping-cart',
                'attributes' => [
                    'id' => 'so-formulir',
                    'data-target' => '/apotek/informasi-formulir/detail?id=',
                    'disabled' => 'true',
                ]
            ];
            $btn_toolbar['delete'] = [
                'attributes' => [
                    'id' => 'delete-formulir',
                    'url' => '/apotek/informasi-formulir/delete?id=',
                    'data-additional' => 'data-rm',
                    'disabled' => 'true',
                ]
            ];
        }

        if((new StokOpname)->check()){
            $btn_toolbar['detail-so'] = [
                'title'=>\Yii::t('fe', 'Detail SO'),
                'icon' => 'fa fa-eye',
                'attributes' => [
                    'id' => 'detail-so',
                    // 'url' => '/apotek/informasi-stok-opname/detail?id=',
                    // 'data-conditions'=> 'stokopname_id',
                    'data-target' => Url::home().('apotek/informasi-stok-opname/detail?id='),
                    'data-conditions' => 'stokopname_id',
                    'disabled' => 'true',
                ]
            ];
        }
         
        return $this->render('inf-stok-formulir-opname', get_defined_vars());
    }

    public function actionGetDataInfStokFormulirOpname()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-stok-opname/inf-stok-formulir-opname?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['formulirstokopname_id']);
                unset($value['formulirstokopname_id']);
                $value['stokopname_id'] = DocoHelpers::encrypt($value['stokopname_id']);
                $value['tglformulir'] = isset($value['tglformulir']) ? date("j M Y H:i:s", strtotime($value['tglformulir'])) : '';
                $value['tglstokopname'] = isset($value['tglstokopname']) ? date("j M Y H:i:s", strtotime($value['tglstokopname'])) : '-';
                $value['inst-ruang'] = $value['instalasi_nama'].' / '.$value['ruangan_nama'];
                if(empty($value['stokopname_id'])){
                    $value['status_verifikasi'] = 'Belum Input Hasil';
                    $value['id_status_verifikasi'] = 2;
                }
                else if($value['is_verifikasi'] == true){
                    $value['status_verifikasi'] = 'Sudah Verifikasi';
                    $value['id_status_verifikasi'] = 1;
                }
                else if($value['is_verifikasi'] == false){
                    $value['status_verifikasi'] = 'Belum Verifikasi';
                    $value['id_status_verifikasi'] = 0;
                }
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
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

        try {
            $response = $this->_restApotek->get('allow/get-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label,
                    'state' => 0,
                ]
            ]);

            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPrintFormulirSoPdf($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/transaksi-formulir-stokopname.pdf";
        $data = Yii::$app->cache->get("formulit-stokopname-{$id}");
        $data['no_formulir'] = $request->get('noformulir',null);
        $data['ruangan_nama'] = $request->get('ruangan_nama',null);
        $data['periode'] = 'tester';
        try {
            $response = $this->_restApotek->post('formulir-stok-opname/print-formulir-stok-opname?id='.$id, [
                'form_params' => $data,
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopup() {
        $request = Yii::$app->request;
        $title = 'Cetak Formulir Stok Opname';
        $id = $request->get('primary', null);
        $params = [
            'id' => DocoHelpers::decrypt($id),
            'noformulir' => $request->get('noformulir', null),
            'ruangan_nama' => $request->get('ruangan_nama', null)
        ];
        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $params);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restApotek, [
            'url' => 'formulir-stok-opname/sync-pdf',
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
        $response = $this->_restApotek->get('formulir-stok-opname/download-pdf', [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    private function getKonfig()
    {
        try {
            $response = $this->guzzleExec($this->_restApotek, [
                'url' => 'allow/konfig-farmasi'
            ]);

            return $response;
        } catch (\Throwable $th) {
            $result['error'] = $th->getMessage();
            return $result;
        }
    }
}
