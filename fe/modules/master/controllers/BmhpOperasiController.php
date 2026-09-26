<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\PaketBmhpForm;
use GuzzleHttp\Exception\RequestException;

class BmhpOperasiController extends DocoController
{
    protected $_title = "BMHP Operasi";
    protected $_module = '/master/bmhp-operasi/';
    protected $_restMaster;
    protected $_restApotek;
    
    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restApotek = Yii::$app->docoRest->apotek;
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
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('bmhp-operasi', 
                [
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['paketbmhp_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['paketbmhp_id']);
                    $value['rowNum'] = $no;
                    $value['qty_pemakaian'] = DocoHelpers::formatNumber($value['qty_pemakaian']);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreate()
    {
        $title = 'Tambah BMHP Operasi';
        $request = Yii::$app->request;
        $model = new PaketBmhpForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = null;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('bmhp-operasi/create', [
                        'form_params' => $model->attributes
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        
        return $this->render('form', get_defined_vars());
    }

    public function actionUpdate($id)
    {
        $title = 'Ubah BMHP Operasi';
        try {
            $id = DocoHelpers::decrypt($id);
            $model = new PaketBmhpForm;
            $request = Yii::$app->request;
            $post = $request->post();
            if ($post) {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $model->load($post);
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'bmhp-operasi/update?id='.$_GET['id'], [
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $request = $this->_restMaster->request('GET', 'bmhp-operasi/view?id='.$id);
                $response = json_decode($request->getBody(), true);
                $attributes = $response['response'];
                $model->attributes = $attributes;
                $model->operasi_nama = $attributes['daftartindakan']['daftartindakan_nama'];
                $model->obatalkes_nama = $attributes['obatalkes']['obatalkes_nama'];
                $model->satuankecil_nama = $attributes['satuankecil']['satuanunit_nama'];
                return $this->render('form', get_defined_vars());
            }
        } catch (Exception $e) {
            $response = [ 
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } catch (RequestException $e) {
            $response = [ 
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        }
        
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        // dump($id);die;
        try {
            $response = $this->_restMaster->delete('bmhp-operasi/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionSearchObatAlkes()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-obat-bmhp',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                // dump($value);die;
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil']['satuanunit_nama'],
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

    public function actionSearchTindakanOperasi()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-tindakan-operasi',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['operasi_nama'],
                    'operasi_kode' => $value['operasi_kode'],
                    'operasi_namalainnya' => $value['operasi_namalainnya'],
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

    public function actionGetListItem()
    {
        $request = Yii::$app->request;
        $cacheBmhp = Yii::$app->cache->get("bmhp-operasi");

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheBmhp !== false) {
            $no = $request->get('start',1);
            foreach ($cacheBmhp as $key => $value) {
                foreach ($value as $k => $v) {
                    $no++;
                    $daftartindakan_id = DocoHelpers::encrypt($key);
                    $obatalkes_id = DocoHelpers::encrypt($v['obatalkes_id']);
                    $data[] = [
                        'rowNum' => $no,
                        'obatalkes_nama' => $v['obatalkes_nama'],
                        'qty_pemakaian' => $v['qty_pemakaian'],
                        'operasi_nama' => $v['operasi_nama'],
                        'satuankecil_nama' => $v['satuankecil_nama'],
                        'aksi' => Html::button(
                            "<i class='fa fa-trash'></i>",[
                                'style' => 'margin-right:5px',
                                'class' => 'btn btn-danger btn-xs delete',
                                'style' => 'margin-right:5px; padding-left:10px !important;',
                                'action' => Url::to([$this->_module .'delete-list-item',
                                    'daftartindakan_id' => $daftartindakan_id,
                                    'obatalkes_id' => $obatalkes_id
                                ]),
                            ]
                        )
                    ];
                }
            }
            $result['data'] = $data;
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
        }

        return DocoHelpers::response($result);
    }

    public function actionSetListItem()
    {
        $request = Yii::$app->request;
        $model = new PaketBmhpForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        $post = $request->post('PaketBmhpForm');
        $model->attributes = $post;
        
        if ($model->validate()) {
            $cacheBmhp = Yii::$app->cache->get("bmhp-operasi");
            if ($cacheBmhp == false) {
                Yii::$app->cache->set("bmhp-operasi",[]);
                $cacheBmhp = [];
            }

            if (!isset($cacheBmhp[$model->daftartindakan_id][$model->obatalkes_id])) {
                $cacheBmhp[$model->daftartindakan_id][$model->obatalkes_id] = [];
            }

            $setCache = [
                'daftartindakan_id' => $post['daftartindakan_id'],
                'operasi_nama' => $post['operasi_nama'],
                'obatalkes_id' => $post['obatalkes_id'],
                'obatalkes_nama' => $post['obatalkes_nama'],
                'satuankecil_id' => $post['satuankecil_id'],
                'satuankecil_nama' => $post['satuankecil_nama'],
                'qty_pemakaian' => $post['qty_pemakaian'],
                
            ];
            
            $cacheBmhp[$model->daftartindakan_id][$model->obatalkes_id] = $setCache;
            $cacheBmhp = Yii::$app->cache->set("bmhp-operasi",$cacheBmhp,3600);
            
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionSave($id = null)
    {
        $post = Yii::$app->request->post();
        $cache = Yii::$app->cache;
        $cacheBmhp = $cache->get("bmhp-operasi");
        $response['response'] = [
            'text' => 'Obat alkes harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;

        if ($cacheBmhp) {
            try {
                $result = $this->_restMaster->post('bmhp-operasi/save',[
                    'form_params' => [
                        'data' => json_encode($cacheBmhp)
                    ]
                ]);
                $result = json_decode($result->getBody(),true);
                // return DocoHelpers::response($result);
                $response['response'] = $result;
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'BMHP Operasi berhasil disimpan',
                    'title' => 'Proses berhasil !'
                ];
                Yii::$app->cache->set("bmhp-operasi",[]);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['message'] = $e->getMessage();
            }
        }
        else {
           try {
               
               $result = $this->_restMaster->post('bmhp-operasi/save?id='.$id,[
                    'form_params' => [
                        'data' => $post
                    ]
                ]);
                $result = json_decode($result->getBody(),true);
                // dump(DocoHelpers::response($result));die;
                // return DocoHelpers::response($result);
                $response['response'] = $result;
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'BMHP Operasi berhasil disimpan',
                    'title' => 'Proses berhasil !'
                ];
           } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['message'] = $e->getMessage();
            }
            
        }
        return DocoHelpers::response($response,$codeHttp);
    }

    public function actionDeleteListItem($daftartindakan_id = null, $obatalkes_id = null)
    {
        $daftartindakan_id = DocoHelpers::decrypt($daftartindakan_id);
        $obatalkes_id = DocoHelpers::decrypt($obatalkes_id);
        $cacheBmhp = Yii::$app->cache->get("bmhp-operasi");
        if ($cacheBmhp !== false) {
            if (isset($cacheBmhp[$daftartindakan_id][$obatalkes_id])) {
                unset($cacheBmhp[$daftartindakan_id][$obatalkes_id]);
                Yii::$app->cache->set("bmhp-operasi",$cacheBmhp);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }
}