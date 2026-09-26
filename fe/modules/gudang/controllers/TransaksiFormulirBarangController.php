<?php 

/**
 * @author Wahyu Saepuloh
 * service : 
 * - Gudang formulir-stok-barang
**/

namespace Doco\gudang\controllers;

use app\components\DHtml;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\gudang\models\TransaksiFormulirBarangForm;
use Doco\gudang\models\TransaksiFormulirBarangDetailForm;
use yii\helpers\ArrayHelper;

class TransaksiFormulirBarangController extends DocoController
{
    // allow sequa blok
    // protected $allowAction = [ '*' ];
    
    protected $_title = "Transaksi Formulir Stok Opname";
    protected $_module = '/gudang/transaksi-formulir-barang';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = DHtml::getTitleMenu($this->_title);
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi = [];
        $data_periode = [];
        
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

        $getkelompok = $this->getKelompok();
        return $this->render('stok-opname', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(!isset($yiiRestfulParams['advanced-filter']['ruangan_id'])){
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        }
        if(!isset($yiiRestfulParams['advanced-filter']['instalasi_id'])){
            $yiiRestfulParams['advanced-filter']['instalasi_id'] = $instalasi_id;
        }
        $draw = $request->get('draw',1);
        $data = [];
        try {
            
            $response = $this->_restGudang->get('transaksi-formulir-barang/', 
                [
                    'form_params' => [],
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['stokfisik'] = "";
                $value['kondisi'] = "";
                $value['stok_sistem'] = DocoHelpers::formatNumber($value['stok_sistem']);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionGetRuangan2()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);

        $additionalPayload = $request->get('additionalPayload', []);

        $response = $this->guzzleExec(Yii::$app->docoRest->master, [
            'url' => 'master-api/get-ruangan',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'instalasi_id' => $request->get('instalasi_id', null),
                    'additionalPayload' => $additionalPayload
                ]
            ]
        ]);

        $resData = [
            'result' => $response,
            'pagination' => [
                'more' => count($response) == 10 ? true : false,
            ],
        ];

        return $this->responseJson(200, 'Data berhasil diambil!', $resData);
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

        $body = $this->guzzleExec(Yii::$app->docoRest->master,[
            'url' => 'master-api/get-ruangan',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'instalasi_id' => $parent_label,
                    'state' => 0,
                ]
            ],
            'returnResponse' => true
        ]);


        if(empty($body['data'])){
            return DocoHelpers::response($result,200);    
        }

        foreach ($body['data'] as $value)
            $result['output'][] = [
                'id' => $value['ruangan_id'],
                'name' => $value['ruangan_nama']
            ];

        return $result;
    }

    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $instalasi_id = empty($post['instalasi_id']) ? null : $post['instalasi_id'];
            $ruangan_id = empty($post['ruangan_id']) ? null : $post['ruangan_id'];
            $namaBarang = empty($post['namaBarang']) ? null : $post['namaBarang'];
            $kelompokBarang = empty($post['kelompokBarang']) ? null : $post['kelompokBarang'];
            $subKelompokBarang = empty($post['subKelompokBarang']) ? null : $post['subKelompokBarang'];
            $listBarangId = json_decode(ArrayHelper::getValue($post, 'listBarangId', []), true); 

            if (!empty($listBarangId)) {
                $where['instalasi_id'] = $instalasi_id;
                $where['ruangan_id'] = $ruangan_id;
                if (!empty($instalasi_id) && !empty($ruangan_id)) {
                    $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
                        'url' => 'transaksi-formulir-barang/save',
                        'method' => 'get',
                        'payload' => [
                            'form_params' => [
                                'namaBarang' => $namaBarang,
                                'kelompokBarang' => $kelompokBarang,
                                'subKelompokBarang' => $subKelompokBarang,
                                'instalasi' => $instalasi_id, 
                                'ruangan_id' => $ruangan_id,
                                'listBarangId' => $listBarangId
                            ],
                        ]
                    ]);
                    return $this->responseJson(200,'Data Stok Opname Berhasil di simpan',$response);
                } else {
                    return $this->responseJson(500,'Terjadi Kesalahan',['text' => 'Periode, Instalasi dan Ruangan harus di isi','title' => 'Proses Gagal !']);
                }
            }else{
                return $this->responseJson(500,'Terjadi Kesalahan',['text' => 'Tidak ada data yg di pilih!','title' => 'Proses Gagal !']);
            }
        } catch (RequestException $e) {
            return $this->responseJson(500,'Terjadi Kesalahan',['title' => 'Terjadi Kesalahan','text' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->responseJson(500,'Terjadi Kesalahan',['title' => 'Terjadi Kesalahan','text' => $e->getMessage()]);
        }
    }

    public function getKelompok()
    {
        $request = $this->_restGudang->request('GET', 'transaksi-formulir-barang/generate-kelompok-api');
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    private function getNamaSubKelompok($subkelompokbarang_id)
    {
        $request = $this->_restGudang->request('GET', 'transaksi-formulir-barang/get-nama-sub-kelompok', [
            'query' => [
                'subkelompokbarang_id' => $subkelompokbarang_id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    public function actionGetSubKelompok($selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = $selected;

        try {
            $response = $this->_restGudang->get('allow/list-sub-kelompok', [
                'query' => [
                    'parent_label' => $parent_label,
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['subkelompokbarang_id'],
                    'name' => $value['subkelompok_nama'],
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

    public function actionBeforePrint($id)
    {
        $title = 'Print formulir Stok Opname';
        $id = DocoHelpers::decrypt($id);
        $data = Yii::$app->cache->get("formulit-stokopname-{$id}");
        $data['periode'] = ($data['periode']) ? $data['periode'] : date('d-m-Y',strtotime($data['data'][0]['tglperiodeposting_awal']))." s/d ".date('d-m-Y',strtotime($data['data'][0]['tglperiodeposting_akhir'])) ; 
        return $this->renderPartial('before-print',get_defined_vars());
    }

    public function actionPrintPdf($id) {
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/transaksi-formulir-stokopname.pdf";
        $this->guzzleExec(Yii::$app->docoRest->gudang, [
			'url' => 'inf-formulir-so-barang/transaksi-print-formulir',
			'method' => 'get',
			'payload' => [
				'save_to' => $path,
                'query' => [
                    'id' => $id
                ]
			]
		]);

        return DocoHelpers::previewPdf($path);
    }
} 