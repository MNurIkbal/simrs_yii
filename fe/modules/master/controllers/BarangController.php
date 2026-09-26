<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\BarangForm;
use GuzzleHttp\Exception\RequestException;

class BarangController extends DocoController
{
    protected $_title = "Barang";
    protected $_module = '/master/barang/';
    protected $_restMaster;
    
    public function init()
    {
        parent::init();
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
        $model = new BarangForm;
        
        $status = $this->_status;
        $response = $this->getRequest();

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
            $response = $this->_restMaster->get('barang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['barang_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['barang_id']);
                    $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);

                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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
        $request = Yii::$app->request;
        $model = new BarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Tambah Barang';
        
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post($formName);
            // $model->barang_thnperoleh = date('Y-m-d', strtotime($post['barang_thnperoleh']));
            $model->nilai_ro = ($post['avg_usage'] * $post['lead_time']) + $post['stok_minimal'];
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('barang/create', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if(isset($body['response']['meta-status'])){
                        return DocoHelpers::response($body['response']['data'], $body['response']['meta-status'], 'BarangForm');
                    }
                    return DocoHelpers::response($body['response']);
                    // return DocoHelpers::responseJsonString($response->getBody(), $formName);
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

        $homeUrl = '/master/barang';
        $model->is_active = 1;
        $listRequest = [
            'data_kelompok'=>'actionListKelompokBarang',
            'data_sub_kelompok'=> 'actionListSubKelompokBarang',
            'data_sk'=>'actionListSatuan',
            'data_ss'=>'actionListSatuan',
            'data_sb'=>'actionListSatuan',
            'data_golongan'=> ['getDataLookup', 'golongan_barang'],
            'data_konfig' => 'actionKonfigAutoGenerateKodeBarang'
        ];
        $response = $this->_restMaster->get('allow/loop-aksi', ['form_params'=>$listRequest]);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];
        $kelompok = (isset($body['data_kelompok']) && count($body['data_kelompok']) > 0) ? ArrayHelper::map($body['data_kelompok'], 'kelompokbarang_id', 'kelompokbarang_nama') : [];
        $golongan = (isset($body['data_golongan']) && count($body['data_golongan']) > 0) ? ArrayHelper::map($body['data_golongan'], 'lookup_id', 'lookup_name') : [];
        $sub_kelompok = (isset($body['data_sub_kelompok']) && count($body['data_sub_kelompok']) > 0) ? ArrayHelper::map($body['data_sub_kelompok'], 'subkelompokbarang_id', 'subkelompok_nama') : [];

        $satuankecil = (isset($body['data_sk']) && count($body['data_sk']) > 0) ? ArrayHelper::map($body['data_sk'], 'satuanunit_id', 'satuanunit_nama') : [];
        $satuanbesar = (isset($body['data_sb']) && count($body['data_sb']) > 0) ? ArrayHelper::map($body['data_sb'], 'satuanunit_id', 'satuanunit_nama') : [];
        $satuansedang = (isset($body['data_ss']) && count($body['data_ss']) > 0) ? ArrayHelper::map($body['data_ss'], 'satuanunit_id', 'satuanunit_nama') : [];
        $autoGenerateKodeBarang = ArrayHelper::getValue($body['data_konfig'], 'is_autogeneratekodebarang', false);
        $disabled = false;

        return $this->render('form', get_defined_vars());
    }

    public function actionUpdate($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $model = new BarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Ubah Barang';
        $disabled = true;
        try {
            if ($request->post()) {
                $model->load($request->post());
                $post = $request->post($formName);
                $model->nilai_ro = ($post['avg_usage'] * $post['lead_time']) + $post['stok_minimal'];
                $model->satuankecil_id = $post['hidden_satuankecil_id'];
                $model->satuan1_id = $post['hidden_satuan1_id'];
                $model->satuan2_id = $post['hidden_satuan2_id'];
                $model->isi_satuan1 = $post['hidden_isi_satuan1'];
                $model->isi_satuan2 = $post['hidden_isi_satuan2'];
                if ($model->validate()) {
                    $response = $this->_restMaster->put('barang/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body,false,'BarangForm');
                    // return DocoHelpers::responseJsonString($response->getBody(), $formName);
                }else{
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $data_update = [];
                try {
                    $response = $this->_restMaster->get('barang/view', [
                        'query' => [
                            'id' => $id
                        ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    $data_update['BarangForm'] = $response['response'];
                    $model->load($data_update);
                    $namaSubKelompok = $this->getNamaSubKelompok($model->subkelompokbarang_id);
                    $model->is_kadaluarsa = isset($response['response']['is_kadaluarsa']) ? $response['response']['is_kadaluarsa'] : '';
                    $model->is_kadaluarsa = ($model->is_kadaluarsa == false) ? 0 : 1;
                    $model->on_ro = isset($response['response']['on_ro']) ? 
                    $response['response']['on_ro'] : 0;

                    $model->on_po = isset($response['response']['on_po']) ? 
                    $response['response']['on_po'] : 0;

                    $model->barang_harganetto = DocoHelpers::formatNumber($model->barang_harganetto);

                    $homeUrl = '/master/barang';
                    $listRequest = [
                        'data_kelompok'=>'actionListKelompokBarang',
                        'data_golongan'=> ['actionGetLookup', 'golongan_barang'],
                        'data_sub_kelompok'=> 'actionListSubKelompokBarang',
                        'data_sk'=>'actionListSatuan',
                        'data_ss'=>'actionListSatuan',
                        'data_sb'=>'actionListSatuan',
                        'data_konfig' => 'actionKonfigAutoGenerateKodeBarang'
                    ];
                    
                    $getdata = $this->_restMaster->get('allow/loop-aksi', ['form_params'=>$listRequest]);
                    $body = json_decode($getdata->getBody(), true);
                    $body = $body['response'];
                    $kelompok = (isset($body['data_kelompok']) && count($body['data_kelompok']) > 0) ? ArrayHelper::map($body['data_kelompok'], 'kelompokbarang_id', 'kelompokbarang_nama') : [];
                    $golongan = (isset($body['data_golongan']) && count($body['data_golongan']) > 0) ? ArrayHelper::map($body['data_golongan'], 'lookup_id', 'lookup_name') : [];
                    $sub_kelompok = (isset($body['data_sub_kelompok']) && count($body['data_sub_kelompok']) > 0) ? ArrayHelper::map($body['data_sub_kelompok'], 'subkelompokbarang_id', 'subkelompok_nama') : [];
                    $satuankecil = (isset($body['data_sk']) && count($body['data_sk']) > 0) ? ArrayHelper::map($body['data_sk'], 'satuanunit_id', 'satuanunit_nama') : [];
                    $satuanbesar = (isset($body['data_sb']) && count($body['data_sb']) > 0) ? ArrayHelper::map($body['data_sb'], 'satuanunit_id', 'satuanunit_nama') : [];
                    $satuansedang = (isset($body['data_ss']) && count($body['data_ss']) > 0) ? ArrayHelper::map($body['data_ss'], 'satuanunit_id', 'satuanunit_nama') : [];
                    $autoGenerateKodeBarang = ArrayHelper::getValue($body['data_konfig'], 'is_autogeneratekodebarang', false);
                } catch (Exception $e) {
                    $data_update['BarangForm'] = [];
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('barang/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        
        try {
            $response = $this->_restMaster->put('barang/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['namaRs'] = Yii::$app->docoVars->identity("nama_rumahsakit");
        $url = 'barang/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Barang.xlsx";
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['namaRs'] = Yii::$app->docoVars->identity("nama_rumahsakit");
            $path = Yii::getAlias("@download") . "/barang.pdf";
            $response = $this->_restMaster->get('barang/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    private function getRequest()
    {
        $request = $this->_restMaster->request('GET', 'barang/generate-api');
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    private function getNamaSubKelompok($subkelompokbarang_id)
    {
        $request = $this->_restMaster->request('GET', 'barang/get-nama-sub-kelompok', [
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
            $response = $this->_restMaster->get('allow/list-sub-kelompok', [
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

    public function actionAutoGenerateKodeBarang($subkelompokbarang_id)
    {
        try {
            $response = $this->_restMaster->get('barang/generate-kode-barang', [
                'query' => [
                    'subkelompokbarang_id' => $subkelompokbarang_id,
                ]
            ]);

            $body = json_decode($response->getBody(), True);
            $result = $body['response']; // new kode barang
                
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionLogPerubahan()
    {
        $title = 'Log Perubahan Barang';
        $id = Yii::$app->request->get('id');
        return $this->renderAjax('_modal_log_perubahan', compact('title', 'id'));
    }

    public function actionGetDataLog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $yiiRestfulParams['id'] = $request->get('id');

        try {
            $response = $this->_restMaster->get('barang/get-log-barang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            $result['data'] = ArrayHelper::getValue($body, 'response.data', []);
            $result['recordsTotal'] = ArrayHelper::getValue($body, 'response._meta.totalCount', 0);
            $result['recordsFiltered'] = ArrayHelper::getValue($body, 'response._meta.totalCount', 0);
            return $result;

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}